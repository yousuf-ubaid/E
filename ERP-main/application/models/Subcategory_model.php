<?php

class Subcategory_model extends ERP_Model
{

    function header_update()
    {

        $this->db->select('description');
        $this->db->where('itemCategoryID', $this->input->post('editid'));
        $this->db->from('srp_erp_itemcategory');
        return $this->db->get()->row_array();
    }


    function save_sub_category()
    {
        //if (!$this->input->post('subcatregoryedit')) {
       // $subCategory=addslashes($this->input->post('subcategory'));
        // echo stripslashes($subCategory);
        //  $itemCategory = $this->db->query("SELECT itemCategoryID,description,companyID FROM srp_erp_itemcategory WHERE companyID = " . $this->common_data['company_data']['company_id'] . " AND description = '" . $subCategory . "' AND masterID='" .$this->input->post('master'). "'")->row_array();

        $subCategory = $this->input->post('subcategory');
        $this->db->select('itemCategoryID,description,companyID');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('description', $subCategory);
        $this->db->where('masterID', $this->input->post('master'));
        $this->db->from('srp_erp_itemcategory');
        $itemCategory= $this->db->get()->row_array();

            if ($itemCategory) {

                $this->session->set_flashdata('e', 'Sub Category already added ');
                return false;

            } else {

                $this->db->set('masterID', (($this->input->post('master') != "")) ? $this->input->post('master') : NULL);

                $this->db->set('description', (($subCategory != "")) ? $subCategory : NULL);
                $this->db->set('revenueGL', (($this->input->post('revnugl') != "")) ? $this->input->post('revnugl') : NULL);
                $this->db->set('costGL', (($this->input->post('costgl') != "")) ? $this->input->post('costgl') : NULL);
                $this->db->set('assetGL', (($this->input->post('assetgl') != "")) ? $this->input->post('assetgl') : NULL);
                $this->db->set('stockAdjustmentGL', (($this->input->post('stockadjust') != "")) ? $this->input->post('stockadjust') : NULL);
                $this->db->set('faCostGLAutoID', (($this->input->post('COSTGLCODEdes') != "")) ? $this->input->post('COSTGLCODEdes') : NULL);
                $this->db->set('faACCDEPGLAutoID', (($this->input->post('ACCDEPGLCODEdes') != "")) ? $this->input->post('ACCDEPGLCODEdes') : NULL);
                $this->db->set('faDEPGLAutoID', (($this->input->post('DEPGLCODEdes') != "")) ? $this->input->post('DEPGLCODEdes') : NULL);
                $this->db->set('faDISPOGLAutoID', (($this->input->post('DISPOGLCODEdes') != "")) ? $this->input->post('DISPOGLCODEdes') : NULL);
                $this->db->set('companyID', (($this->common_data['company_data']['company_id'] != "")) ? $this->common_data['company_data']['company_id'] : NULL);
                $this->db->set('companyCode', $this->common_data['company_data']['company_code']);
                $this->db->set('createdPCID', (($this->input->post('createdpcid') != "")) ? $this->input->post('createdpcid') : NULL);
                $this->db->set('createdUserID', (($this->input->post('createduserid') != "")) ? $this->input->post('createduserid') : NULL);
                $this->db->set('createdUserName', (($this->input->post('createdusername') != "")) ? $this->input->post('createdusername') : NULL);
                $this->db->set('createdDateTime', (($this->input->post('createddate') != "")) ? $this->input->post('createddate') : NULL);
                $this->db->set('codePrefix', (($this->input->post('codePrefix') != "")) ? $this->input->post('codePrefix') : NULL);
                $result = $this->db->insert('srp_erp_itemcategory');
                if ($result) {
                    $this->session->set_flashdata('s', 'Sub Category Added Successfully');
                    return true;
                }
            }
        //}
        /*else{

            $data['ClientID'] = $this->input->post('clientidedit');
            $data['Client'] = $this->input->post('clientdescription');
            $this->db->where('ClientID', $this->input->post('clientidedit'));
            $result = $this->db->update('clients', $data);
            if($result){
                $this->session->set_flashdata('s', 'Records Updated Successfully');
                return true;
            }
        }*/
    }

    function edit_itemsubcategory()
    {
        $this->db->select('*');
        $this->db->where('itemCategoryID', $this->input->post('id'));
        return $this->db->get('srp_erp_itemcategory')->row_array();
    }

    function update_itemsubcategory()
    {
        $itemCategoryID=$this->input->post('subcatregoryeditfrm');
        $subCategory = $this->input->post('description');

        $this->db->select('itemCategoryID,description,companyID');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('description', $subCategory);
        $this->db->where('masterID', $this->input->post('master'));
        $this->db->where('itemCategoryID !=', $itemCategoryID);
        $this->db->from('srp_erp_itemcategory');
        $itemCategory= $this->db->get()->row_array();

        if ($itemCategory) {
            return array('e', 'Sub Category already added');
            //$this->session->set_flashdata('e', 'Sub Category already added ');
            return false;

        } else {
            $prefix = $this->db->query("SELECT itemCategoryID FROM srp_erp_itemcategory WHERE companyID = " . $this->common_data['company_data']['company_id'] . " AND codePrefix = '" . $this->input->post('codePrefix') . "' AND itemCategoryID != '" . $this->input->post('subcatregoryeditfrm') . "'")->row_array();

            if ($prefix)
            {
                return array('e', 'Prefix already added');
            }
            $data['description'] = ((($this->input->post('description') != "")) ? $this->input->post('description') : NULL);
            $data['codePrefix'] = ((($this->input->post('codePrefix') != "")) ? $this->input->post('codePrefix') : NULL);
            $data['revenueGL'] = ((($this->input->post('revnugledit') != "")) ? $this->input->post('revnugledit') : NULL);
            $data['costGL'] = ((($this->input->post('costgledit') != "")) ? $this->input->post('costgledit') : NULL);
            $data['assetGL'] = ((($this->input->post('assetgledit') != "")) ? $this->input->post('assetgledit') : NULL);
            $data['stockAdjustmentGL'] = ((($this->input->post('stockadjustedit') != "")) ? $this->input->post('stockadjustedit') : NULL);

            $data['faACCDEPGLAutoID'] = ((($this->input->post('ACCDEPGLCODEdes_edit') != "")) ? $this->input->post('ACCDEPGLCODEdes_edit') : NULL);
            $data['faCostGLAutoID'] = ((($this->input->post('COSTGLCODEdes_edit') != "")) ? $this->input->post('COSTGLCODEdes_edit') : NULL);
            $data['faDEPGLAutoID'] = ((($this->input->post('DEPGLCODEdes_edit') != "")) ? $this->input->post('DEPGLCODEdes_edit') : NULL);
            $data['faDISPOGLAutoID'] = ((($this->input->post('DISPOGLCODEdes_edit') != "")) ? $this->input->post('DISPOGLCODEdes_edit') : NULL);

            $this->db->where('itemCategoryID', $this->input->post('subcatregoryeditfrm'));
            $result = $this->db->update('srp_erp_itemcategory', $data);
            if ($result) {
                return array('s', 'Data Updated Successfully');
            }else{
                return array('w', 'No changes found');
            }
        }
    }

    function save_sub_sub_category()
    {
        $assignBuyersPolicy = getPolicyValues('ABFC', 'All');
        if (!$this->input->post('subsubedit')) {

            $subCategoryID = $this->input->post('subsubcategoryedit');
            $description = $this->input->post('subsubcategory[]');
            $codePrefix = $this->input->post('codePrefix[]');
            foreach ($description as $key => $de) {
                $this->db->select('itemCategoryID,description,companyID');
                $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                $this->db->where('description', $de);
                $this->db->where('masterID', $subCategoryID);
                $this->db->from('srp_erp_itemcategory');
                $itemCategory= $this->db->get()->row_array();
                if ($itemCategory) {
                    return array('e', $de.' Sub Sub Category already added ');
                } else {
                    $prefix = $this->db->query("SELECT itemCategoryID FROM srp_erp_itemcategory WHERE companyID = " . $this->common_data['company_data']['company_id'] . " AND codePrefix = '" . $codePrefix[$key] . "'")->row_array();

                    if ($prefix)
                    {
                        return array('e', 'Prefix ' . $codePrefix[$key] . ' already added');
                    }
                    $this->db->set('masterID', (($this->input->post('subsubcategoryedit') != "")) ? $this->input->post('subsubcategoryedit') : NULL);
                    $this->db->set('description',$de);
                    $this->db->set('codePrefix',$codePrefix[$key]);
                    $this->db->set('revenueGL', (($this->input->post('rvgl') != "")) ? $this->input->post('rvgl') : NULL);
                    $this->db->set('costGL', (($this->input->post('cstgl') != "")) ? $this->input->post('cstgl') : NULL);
                    $this->db->set('assetGL', (($this->input->post('astgl') != "")) ? $this->input->post('astgl') : NULL);
                    $this->db->set('companyID', (($this->common_data['company_data']['company_id'] != "")) ? $this->common_data['company_data']['company_id'] : NULL);
                    $this->db->set('companyCode', $this->common_data['company_data']['company_code']);
                    $result = $this->db->insert('srp_erp_itemcategory');
                   
                }
            }
            if ($result) {
                return array('s', 'Sub Sub Category Added Successfully');
            }
           
        }
        /*else{

            $data['ClientID'] = $this->input->post('clientidedit');
            $data['Client'] = $this->input->post('clientdescription');
            $this->db->where('ClientID', $this->input->post('clientidedit'));
            $result = $this->db->update('clients', $data);
            if($result){
                $this->session->set_flashdata('s', 'Records Updated Successfully');
                return true;
            }
        }*/
    }

    function add_buyers_to_category(){
        $buyers_subsub = $this->input->post("buyers_for_cat");
        $selected_cat_id = $this->input->post("selected_cat_id");
        $type = $this->input->post("selected_cat_type");

        $this->db->select('*');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        $this->db->where('itemCategoryID', $selected_cat_id);
        $this->db->from('srp_erp_itemcategory');
        $itemCategory_arr= $this->db->get()->row_array();

        $buyers_subsub_str=NULL;
        if(count($buyers_subsub)>0){
            $buyers_subsub_str = implode(",",$buyers_subsub);
        }

        if($type==1){

            if(count($buyers_subsub)>0){
                foreach($buyers_subsub as $val){

                    $this->db->select('*');
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('subCatID', $selected_cat_id);
                    $this->db->where('empID', $val);
                    $this->db->where('subSubCatID', NULL);
                    $this->db->where('userType', 0);
                    $this->db->from('srp_erp_incharge_assign');
                    $added_emp= $this->db->get()->row_array();

                    if(!$added_emp){
                        $data_buyer['documentID'] = $itemCategory_arr['codePrefix'];
                        $data_buyer['catID'] =$itemCategory_arr['masterID'];
                        //$data_buyer['subCatID'] = $itemCategory_arr['masterID'];
                        $data_buyer['subCatID'] = $itemCategory_arr['itemCategoryID'];
                        $data_buyer['empID'] = $val;
                        $data_buyer['companyID'] = $this->common_data['company_data']['company_id'];
                        $data_buyer['createdUserGroup'] = $this->common_data['user_group'];
                        $data_buyer['createdPCID'] = $this->common_data['current_pc'];
                        $data_buyer['createdUserID'] =  $this->common_data['current_userID'];
                        $data_buyer['createdDateTime'] = $this->common_data['current_date'];
                        $data_buyer['createdUserName'] = $this->common_data['current_user'];
                
                        $this->db->insert('srp_erp_incharge_assign', $data_buyer);
                    }
                }
            }

        }else{

            if(count($buyers_subsub)>0){
                foreach($buyers_subsub as $val){

                    $this->db->select('*');
                    $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                    $this->db->where('subSubCatID', $selected_cat_id);
                    $this->db->where('userType', 0);
                    $this->db->where('empID', $val);
                    $this->db->from('srp_erp_incharge_assign');
                    $added_emp_sub_sub= $this->db->get()->row_array();

                    if(!$added_emp_sub_sub){
                        $this->db->select('*');
                        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
                        $this->db->where('itemCategoryID', $itemCategory_arr['masterID']);
                        $this->db->from('srp_erp_itemcategory');
                        $itemCategory_arr_master= $this->db->get()->row_array();

                        $data_buyer['documentID'] = $itemCategory_arr['codePrefix'];
                        $data_buyer['catID'] =$itemCategory_arr_master['masterID'];
                        $data_buyer['subCatID'] = $itemCategory_arr['masterID'];
                        $data_buyer['subSubCatID'] = $itemCategory_arr['itemCategoryID'];
                        $data_buyer['empID'] = $val;
                        $data_buyer['companyID'] = $this->common_data['company_data']['company_id'];
                        $data_buyer['createdUserGroup'] = $this->common_data['user_group'];
                        $data_buyer['createdPCID'] = $this->common_data['current_pc'];
                        $data_buyer['createdUserID'] =  $this->common_data['current_userID'];
                        $data_buyer['createdDateTime'] = $this->common_data['current_date'];
                        $data_buyer['createdUserName'] = $this->common_data['current_user'];
                
                        $this->db->insert('srp_erp_incharge_assign', $data_buyer);
                    }
                }
            }

           
        }
  

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return array('e', ' Update Failed ' . $this->db->_error_message());
            $this->db->trans_rollback();
        
        } else {
            return array('s', 'Updated Successfully.','');
            $this->db->trans_commit();
    
        }
    }

    function edit_itemsubsubcategory()
    {
        $this->db->select('*');
        $this->db->where('itemCategoryID', $this->input->post('id'));
        return $this->db->get('srp_erp_itemcategory')->row_array();
    }


    function update_subsubcategory()
    {

        $itemCategoryID = $this->input->post('subsubcatregoryeditfrm');
        $description = $this->input->post('descriptionsubsub');
        $codePrefix = $this->input->post('codePrefix');

        //Get sub category id for the sub sub category
        $this->db->select('masterID');
        $this->db->where('companyID', $this->common_data['company_data']['company_id']);
        //$this->db->where('description', $description);
        $this->db->where('itemCategoryID', $itemCategoryID);
        $this->db->from('srp_erp_itemcategory');
        $masterID= $this->db->get()->row('masterID');

        if ($masterID) {
            // Validate the Sub Sub Category duplication
            $this->db->select('itemCategoryID,description,companyID');
            $this->db->where('companyID', $this->common_data['company_data']['company_id']);
            $this->db->where('description', $description);
            $this->db->where('masterID', $masterID);
            $this->db->where('itemCategoryID !=', $itemCategoryID);
            $this->db->from('srp_erp_itemcategory');
            $itemCategory= $this->db->get()->row_array();

            if ($itemCategory) {
                return array('e', 'Sub Sub Category already added');
                //$this->session->set_flashdata('e', 'Sub Category already added ');
                return false;
            } else {
                $prefix = $this->db->query("SELECT itemCategoryID FROM srp_erp_itemcategory WHERE companyID = " . $this->common_data['company_data']['company_id'] . " AND codePrefix = '" . $codePrefix . "' AND itemCategoryID !=" . $itemCategoryID)->row_array();

                if ($prefix)
                {
                    return array('e', 'Prefix already added');
                }
                $data['description'] = ((($description != "")) ? $description : NULL);
                $data['codePrefix'] = ((($codePrefix != "")) ? $codePrefix : NULL);
                $this->db->where('itemCategoryID', $itemCategoryID);
                $result = $this->db->update('srp_erp_itemcategory', $data);
                if ($result) {
                    return array('s', 'Data Updated Successfully');
                }
            }
        }else{
            return array('e', 'Sub Category not found');
            return false;
        }
    }

    function delete_category_assign_buyers(){
        $id = $this->input->post('id');

        $this->db->trans_start();

        $this->db->select('*');
        $this->db->where('assignMasterID', $id);
        $this->db->from('srp_erp_incharge_assign');
        $assign= $this->db->get()->result_array();

        if(!empty($assign)){
            return array('w','Buyer Already Assigned');
        }else{
            $this->db->where('autoID', $id)->delete('srp_erp_incharge_assign');
        }
    
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e','Update Fail');
        } else {
            
            $this->db->trans_commit();
            return array('s','Successfully Deleted');
        }
    }


}
