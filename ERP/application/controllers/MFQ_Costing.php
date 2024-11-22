<?php
class MFQ_Costing extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('MFQ_Costing_model');
    }

    function fetch_costing_entry_setup()
    {
        $data['details'] = $this->MFQ_Costing_model->fetch_costing_entry_setup();
        $this->load->view('system/mfq/mfq_costing_view', $data);
    }

    function enable_cost_entry()
    {
        $this->form_validation->set_rules("costingID", 'costing ID', 'required');
        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->MFQ_Costing_model->enable_cost_entry());
        }
    }

    function enable_manual_cost_entry()
    {
        $this->form_validation->set_rules("costingID", 'costing ID', 'required');
        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->MFQ_Costing_model->enable_manual_cost_entry());
        }
    }

    function enable_linkedDoc_cost_entry()
    {
        $this->form_validation->set_rules("costingID", 'costing ID', 'required');
        if ($this->form_validation->run() == FALSE) {
            $msg = explode('</p>', validation_errors());
            $trimmed_array = array_map('trim', $msg);
            $uniqMesg = array_unique($trimmed_array);
            $validateMsg = array_map(function ($uniqMesg) {
                return $a = $uniqMesg . '</p>';
            }, array_filter($uniqMesg));
            echo json_encode(array('e', join('', $validateMsg)));
        } else {
            echo json_encode($this->MFQ_Costing_model->enable_linkedDoc_cost_entry());
        }
    }

    function fetch_tem_configuration_table()
    {
        $this->datatables->select('itemAutoID as itemAutoID,itemSystemCode,itemName,seconeryItemCode,itemImage,itemDescription,mainCategoryID,mainCategory,defaultUnitOfMeasure,currentStock,companyLocalSellingPrice,companyLocalCurrency,companyLocalCurrencyDecimalPlaces,revanueDescription,costDescription,assteDescription,isActive,companyLocalWacAmount,srp_erp_itemcategory.description as SubCategoryDescription,CONCAT(currentStock,\'  \',defaultUnitOfMeasure) as CurrentStock,CONCAT(companyLocalWacAmount,\'  \',companyLocalCurrency) as TotalWacAmount', false)
            ->from('srp_erp_itemmaster')
            ->join('srp_erp_itemcategory', 'srp_erp_itemmaster.subcategoryID = srp_erp_itemcategory.itemCategoryID');
        $this->datatables->where('srp_erp_itemmaster.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->where('srp_erp_itemmaster.isActive', 1);
        $this->datatables->where('srp_erp_itemmaster.isMfqItem', 1);
        $this->datatables->where('srp_erp_itemmaster.masterApprovedYN', 1);
        $this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        echo $this->datatables->generate();
    }

    function fetch_item_config_sync_item()
    {
        $this->datatables->select('itemAutoID AS itemAutoID,itemSystemCode,itemName,seconeryItemCode,itemImage,itemDescription,mainCategoryID,mainCategory,defaultUnitOfMeasure,currentStock,companyLocalSellingPrice,companyLocalCurrency,companyLocalCurrencyDecimalPlaces,revanueDescription,costDescription,assteDescription,isActive,companyLocalWacAmount,srp_erp_itemcategory.description as SubCategoryDescription,CONCAT(currentStock,\'  \',defaultUnitOfMeasure) as CurrentStock,CONCAT(companyLocalWacAmount,\'  \',companyLocalCurrency) as TotalWacAmount', false)
            ->from('srp_erp_itemmaster')
            ->join('srp_erp_itemcategory', 'srp_erp_itemmaster.subcategoryID = srp_erp_itemcategory.itemCategoryID');
        $this->datatables->where('srp_erp_itemmaster.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->where('srp_erp_itemmaster.isActive', 1);
        $this->datatables->where('srp_erp_itemmaster.isMfqItem != 1');
        $this->datatables->where('srp_erp_itemmaster.mainCategory != "Fixed Assets"');
        $this->datatables->where('srp_erp_itemmaster.masterApprovedYN', 1);
        if (!empty($this->input->post('mainCategory'))) {
            $this->datatables->where('mainCategoryID', $this->input->post('mainCategory'));
        } else {
            $this->datatables->where('srp_erp_itemmaster.mainCategory IN ("Inventory","Non Inventory","Service","Services")');
        }
        if (!empty($this->input->post('subcategory'))) {
            $this->datatables->where('subcategoryID', $this->input->post('subcategory'));
        }
        $this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="ItemsSelectedSync(this)" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'itemAutoID');
        echo $this->datatables->generate();
    }

    function configure_item()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Item', 'trim|required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata($msgtype = 'e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->MFQ_Costing_model->configure_item());
        }
    }

    function save_new_gl_configuration()
    {
        $this->form_validation->set_rules('itemCategory', 'Item Category', 'trim|required');
        $itemCategory = $this->input->post('itemCategory');
        if($itemCategory == 'Inventory') {
            $this->form_validation->set_rules('glAutoID_inv', 'GL Code', 'trim|required');
        } else {
            $this->form_validation->set_rules('glAutoID_srv', 'GL Code', 'trim|required');
        }
        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('e', validation_errors()));
        } else {
            echo json_encode($this->MFQ_Costing_model->save_new_gl_configuration());
        }
    }

    function fetch_gl_configuration_table()
    {
        $this->datatables->select('configurationAutoID, configurationCode, GLAutoID, systemAccountCode, GLSecondaryCode, GLDescription', false)
            ->from('srp_erp_mfq_postingconfiguration')
            ->join('srp_erp_chartofaccounts', 'srp_erp_chartofaccounts.GLAutoID = srp_erp_mfq_postingconfiguration.value');
        $this->datatables->where('srp_erp_mfq_postingconfiguration.companyID', $this->common_data['company_data']['company_id']);
        $this->datatables->add_column('edit', '<span class="pull-right"><a onclick="edit_gl_config($1, \'$2\', $3);"><span title="Edit" rel="tooltip" class="glyphicon glyphicon-pencil"></span></a></span>', 'configurationAutoID, configurationCode, GLAutoID');
        echo $this->datatables->generate();
    }
}