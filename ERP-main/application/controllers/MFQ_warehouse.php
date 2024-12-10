<?php

class MFQ_warehouse extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('MFQ_warehouse_model');
        $this->load->helper('mfq');
    }

    function fetch_warehouse()
    {
        $this->datatables->select('mfq_wm.mfqWarehouseAutoID as mfq_mfqWarehouseAutoID ,mfq_wm.companyCode as mfq_companyCode,mfq_wm.warehouseCode as mfq_warehouseCode,mfq_wm.warehouseDescription as mfq_warehouseDescription,mfq_wm.warehouseLocation as mfq_warehouseLocation,mfq_wm.isFromERP as mfq_isFromERP ,mfq_wm.warehouseAutoID as mfq_warehouseAutoID ,wm.wareHouseDescription as erpwareHouseDescription',false)
            ->from('srp_erp_mfq_warehousemaster mfq_wm')
            ->join('(SELECT wareHouseAutoID,wareHouseDescription FROM srp_erp_warehousemaster WHERE companyID ='.$this->common_data['company_data']['company_id'].') wm', 'mfq_wm.warehouseAutoID = wm.wareHouseAutoID', 'LEFT')
            ->where('mfq_wm.companyID',current_companyID())
            ->add_column('action', '$1', 'edit_mfq_warehouse(mfq_mfqWarehouseAutoID, mfq_isFromERP,mfq_warehouseAutoID)');
        echo $this->datatables->generate();
    }

    function fetch_sync_warehouse()
    {
        $this->datatables->select('masterTbl.warehouseAutoID as warehouseAutoID,masterTbl.companyCode,masterTbl.warehouseCode,masterTbl.warehouseDescription,masterTbl.warehouseLocation', false)
            ->from('srp_erp_warehousemaster as masterTbl')
            ->where('warehouseType', 2)
            ->where('companyID', current_companyID());
        $this->datatables->where('NOT EXISTS(SELECT * FROM srp_erp_mfq_warehousemaster WHERE srp_erp_mfq_warehousemaster.warehouseAutoID = masterTbl.warehouseAutoID AND companyID =' . current_companyID() . ' )');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="ItemsSelectedSync(this)" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'warehouseAutoID');
        $result = $this->datatables->generate();
        echo $result;
    }

    function fetch_link_warehouse()
    {
        $this->datatables->select('masterTbl.warehouseAutoID as warehouseAutoID,masterTbl.companyCode,masterTbl.warehouseCode,masterTbl.warehouseDescription,masterTbl.warehouseLocation', false)
            ->from('srp_erp_warehousemaster as masterTbl')
            ->where('companyID', current_companyID());
        $this->datatables->where('NOT EXISTS(SELECT * FROM srp_erp_mfq_warehousemaster WHERE srp_erp_mfq_warehousemaster.warehouseAutoID = masterTbl.warehouseAutoID AND companyID =' . current_companyID() . ' )');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="linkWarehouse_$1" name="linkItem" type="radio"  value="$1" class="radioChk" data-warehouseAutoID="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'warehouseAutoID');
        $result = $this->datatables->generate();
        echo $result;
    }

    function add_warehouse()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Item', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->MFQ_warehouse_model->add_warehouse());
        }
    }

    function save_warehouse()
    {
        $this->form_validation->set_rules('warehouseDescription', 'Warehouse Description', 'trim|required');
        $this->form_validation->set_rules('warehouseLocation', 'Warehouse Location', 'trim|required');
        $this->form_validation->set_rules('warehouseCode', 'Warehouse Code.', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->MFQ_warehouse_model->save_warehouse());
        }
    }

    function edit_warehouse()
    {
        $id = $this->input->post('mfqWarehouseAutoID');
        if ($id != "") {
            echo json_encode($this->MFQ_warehouse_model->get_warehouse());
        } else {
            echo json_encode(FALSE);
        }
    }

    function link_warehouse()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Item', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->MFQ_warehouse_model->link_warehouse());
        }
    }

}
