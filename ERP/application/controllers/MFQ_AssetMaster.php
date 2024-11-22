<?php

class MFQ_AssetMaster extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('MFQ_asset_model');
        $this->load->helper('mfq');

    }

    function fetch_asset()
    {
        $search = $_REQUEST["sSearch"];
        $this->datatables->select('masterTbl.*, masterTbl.mfq_faID as autoID,rowtbl.assetDescription as masterassetDescription, mfqMasterCategory.description as mainCatDescription, mfqMasterSubCategory.description as subCatDescription, mfqMasterSubSubCategory.description as subSubCatDescription, masterTbl.assetDescription as assetDesc, isFromERP as isFromERP', false)
                ->from('srp_erp_mfq_fa_asset_master as masterTbl')
            ->join('srp_erp_fa_asset_master as rowtbl', 'masterTbl.faID = rowtbl.faID', 'LEFT')
            ->join('srp_erp_mfq_category mfqMasterCategory', 'mfqMasterCategory.itemCategoryID = masterTbl.mfq_faCatID', 'LEFT')
            ->join('srp_erp_mfq_category mfqMasterSubCategory', 'mfqMasterSubCategory.itemCategoryID = masterTbl.mfq_faSubCatID', 'LEFT')
            ->join('srp_erp_mfq_category mfqMasterSubSubCategory', 'mfqMasterSubSubCategory.itemCategoryID = masterTbl.mfq_faSubSubCatID', 'LEFT')->where('masterTbl.companyID',current_companyID());
        $this->datatables->add_column('mfq_category', '$1', 'col_category(autoID, mainCatDescription,add_mainCategory,assetDesc)');
        $this->datatables->add_column('mfq_subCategory', '$1', 'col_category(autoID, subCatDescription,add_mainCategory,assetDesc)');
        $this->datatables->add_column('mfq_subSubCategory', '$1', 'col_category(autoID, subSubCatDescription,add_mainCategory,assetDesc)');
        $this->datatables->add_column('confirmed', '$1', ''); /*confirm_mfq(isActive)*/
        //$this->datatables->add_column('edit', '$1', 'edit(itemAutoID,isActive)');
        $this->datatables->add_column('edit', '$1', 'edit_mfq_asset(autoID,isFromERP)');
        if($search){
            $this->datatables->like('masterTbl.assetDescription', $search);
            $this->datatables->or_like('masterTbl.faCode', $search);
        }

        $r = $this->datatables->generate();
        //echo $this->db->last_query();

        echo $r;
    }

    function fetch_sync_asset()
    {

        $companyID = current_companyID();
        $this->datatables->select('*, srp_erp_fa_asset_master.faID as KeyValue', false)
            ->from('srp_erp_fa_asset_master');
        $this->datatables->where('NOT EXISTS(SELECT * FROM srp_erp_mfq_fa_asset_master WHERE srp_erp_mfq_fa_asset_master.faID = srp_erp_fa_asset_master.faID AND companyID =' . $companyID . ' )');
        $this->datatables->where('srp_erp_fa_asset_master.companyID', $companyID);
        //$this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="ItemsSelectedSync(this)" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'KeyValue');
        echo $this->datatables->generate();
    }
    function fetch_link_asset()
    {
        $companyID = current_companyID();
        $this->datatables->select('*, srp_erp_fa_asset_master.faID as KeyValue', false)
            ->from('srp_erp_fa_asset_master');
        $this->datatables->where('NOT EXISTS(SELECT * FROM srp_erp_mfq_fa_asset_master WHERE srp_erp_mfq_fa_asset_master.faID = srp_erp_fa_asset_master.faID AND companyID =' . $companyID . ' )');
        $this->datatables->where('srp_erp_fa_asset_master.companyID', $companyID);
        //$this->datatables->add_column('item_inventryCode', '$1 - $2 <b></b>', 'itemSystemCode,itemDescription');
        $this->datatables->add_column('confirmed', '$1', 'confirm(isActive)');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="linkItem_$1" name="linkItem" type="radio"  value="$1" class="radioChk" data-itemAutoID="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'KeyValue');

        $result = $this->datatables->generate();
        echo $result;
    }

    function link_asset()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Item', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->MFQ_asset_model->link_asset());
        }
    }


    function add_Asset()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Item', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->MFQ_asset_model->add_asset());
        }
    }

    function assign_itemCategory_children()
    {
        $validation = $this->mfq_validate_category();
        if ($validation['error'] == 1) {
            echo json_encode(array('error' => 1, 'message' => $validation['message']));
        } else {

            $itemAutoID = $this->input->post('itemAutoID');
            $data['mfq_faCatID'] = $this->input->post('categoryID');
            $data['mfq_faSubCatID'] = $this->input->post('subCategory');
            $data['mfq_faSubSubCatID'] = $this->input->post('subSubCategory');

            $result = $this->MFQ_asset_model->update_srp_erp_mfq_fa_asset_master($itemAutoID, $data);
            if ($result) {
                echo json_encode(array('error' => 0, 'message' => 'Record updated successfully!'));
            } else {
                echo json_encode(array('error' => 1, 'message' => 'Error, while updating.'));
            }
        }
    }

    function mfq_validate_category()
    {
        $category = $this->input->post('categoryID');
        $subCategory = $this->input->post('subCategory');
        $subSubCategory = $this->input->post('subSubCategory');
        if ($category == -1 || $category == '') {
            return array('error' => 1, 'message' => 'Category is required.');
        } else if ($subCategory == -1 || $subCategory == '') {
            return array('error' => 1, 'message' => 'Sub Category is required.');
        } else if ($subSubCategory == -1 || $subSubCategory == '') {
            return array('error' => 1, 'message' => 'Sub Sub Category is required.');
        } else {
            return array('error' => 0);

        }
    }

    function add_edit_mfq_machine()
    {
        $mfqItemID = $this->input->post('mfq_faID');

        $this->form_validation->set_rules('assetDescription', 'Machine Name', 'trim|required');
        $this->form_validation->set_rules('partNumber', 'Part No', 'trim|required');
        $this->form_validation->set_rules('mfq_faCatID', 'Category', 'trim|required');

        $flowserve = getPolicyValues('MANFL', 'All');

        if($flowserve =='FlowServe'){
            $this->form_validation->set_rules('from_date', 'From Date', 'trim|required');
            $this->form_validation->set_rules('to_date', 'To Date', 'trim|required');
        }
      /*  $this->form_validation->set_rules('mfq_faSubCatID', 'Sub Category', 'trim|required');
        $this->form_validation->set_rules('mfq_faSubSubCatID', 'Sub Sub Cateogry', 'trim|required');*/

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('error' => 1, 'message' => validation_errors()));
        } else {
            if ($mfqItemID) {
                /** Update */

                echo json_encode($this->MFQ_asset_model->update_machine());
            } else {
                /** Insert */
                echo json_encode($this->MFQ_asset_model->insert_machine());
            }
        }
    }

    function load_mfq_Machine()
    {
        $mfq_faID = $this->input->post('mfq_faID');
        $mfqAssetMaster = $this->MFQ_asset_model->get_srp_erp_mfq_fa_asset_master($mfq_faID);
        if (!empty($mfqAssetMaster)) {
            echo json_encode(array_merge(array('error' => 0, 'message' => 'Machine Loaded'), $mfqAssetMaster));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'no record found!'));
        }
    }

}
