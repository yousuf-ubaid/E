<?php
class Itemcategorygroup_model extends ERP_Model{

    function save_item_category(){
        $data['codePrefix']          = $this->input->post('codeprefix');
        $data['StartSerial']         = $this->input->post('startserial');
        $data['codeLength']          = $this->input->post('codelength');
        $data['description']         = $this->input->post('description');
        $data['itemType']            = $this->input->post('itemtype');
        $data['categoryTypeID']      = $this->input->post('categoryTypeID');
        $data['modifiedPCID']        = $this->common_data['current_pc'];
        $data['modifiedUserID']      = $this->common_data['current_userID'];
        $data['modifiedUserName']    = $this->common_data['current_user'];
        $data['modifiedDateTime']    = $this->common_data['current_date'];

        if($this->input->post('itemcategoryedit')){
            $this->db->where('itemCategoryID', $this->input->post('itemcategoryedit'));
            $this->db->update('srp_erp_itemcategory', $data);
            $this->session->set_flashdata('s', 'Item Category Updated Successfully');
            return true;
        }
        else{
            $data['companyID']                    = $this->common_data['company_data']['company_id'];
            $data['companyCode']                  = $this->common_data['company_data']['company_code'];
            $data['createdUserGroup']             = $this->common_data['user_group'];
            $data['createdPCID']                  = $this->common_data['current_pc'];
            $data['createdUserID']                = $this->common_data['current_userID'];
            $data['createdUserName']              = $this->common_data['current_user'];
            $data['createdDateTime']              = $this->common_data['current_date'];
            $this->db->insert('srp_erp_itemcategory',$data);
            $this->session->set_flashdata('s', 'Item Category Created Successfully');
            return true;
        }
    }

    function edit_itemcategory()
    {
        $this->db->select('*');
        $this->db->where('itemCategoryID', $this->input->post('id'));
        return $this->db->get('srp_erp_itemcategory')->row_array();
    }

    function save_sub_category()
    {
        $companyid = current_companyID();
        /*$this->db->select('companyGroupID');
        $this->db->where('companyID', $companyid);
        $grp = $this->db->get('srp_erp_companygroupdetails')->row_array();*/
        $grpid = $companyid;

        if (!$this->input->post('subcatregoryedit')) {

            $itemCategory = $this->db->query("SELECT itemCategoryID,description FROM srp_erp_groupitemcategory WHERE groupID = " . $grpid . " AND description = '" . $this->input->post('subcategory') . "'")->row_array();

            if ($itemCategory) {

                $this->session->set_flashdata('e', 'Sub Category already added ');
                return false;

            } 

            $prefix = $this->db->query("SELECT itemCategoryID,description FROM srp_erp_groupitemcategory WHERE groupID = " . $grpid . " AND codePrefix = '" . $this->input->post('codePrefix') . "'")->row_array();

            if ($prefix)
            {
                $this->session->set_flashdata('e', 'Prefix already added');
                return false;
            } 

            $this->db->set('masterID', (($this->input->post('master') != "")) ? $this->input->post('master') : NULL);
            $this->db->set('groupID', $grpid);

            $this->db->set('description', (($this->input->post('subcategory') != "")) ? $this->input->post('subcategory') : NULL);
            $this->db->set('codePrefix', (($this->input->post('codePrefix') != "")) ? $this->input->post('codePrefix') : NULL);
            /*$this->db->set('revenueGL', (($this->input->post('revnugl') != "")) ? $this->input->post('revnugl') : NULL);
            $this->db->set('costGL', (($this->input->post('costgl') != "")) ? $this->input->post('costgl') : NULL);
            $this->db->set('assetGL', (($this->input->post('assetgl') != "")) ? $this->input->post('assetgl') : NULL);
            $this->db->set('faCostGLAutoID', (($this->input->post('COSTGLCODEdes') != "")) ? $this->input->post('COSTGLCODEdes') : NULL);
            $this->db->set('faACCDEPGLAutoID', (($this->input->post('ACCDEPGLCODEdes') != "")) ? $this->input->post('ACCDEPGLCODEdes') : NULL);
            $this->db->set('faDEPGLAutoID', (($this->input->post('DEPGLCODEdes') != "")) ? $this->input->post('DEPGLCODEdes') : NULL);
            $this->db->set('faDISPOGLAutoID', (($this->input->post('DISPOGLCODEdes') != "")) ? $this->input->post('DISPOGLCODEdes') : NULL);*/
            /*$this->db->set('companyID', (($this->common_data['company_data']['company_id'] != "")) ? $this->common_data['company_data']['company_id'] : NULL);
            $this->db->set('companyCode', $this->common_data['company_data']['company_code']);*/
            $this->db->set('createdPCID', (($this->input->post('createdpcid') != "")) ? $this->input->post('createdpcid') : NULL);
            $this->db->set('createdUserID', (($this->input->post('createduserid') != "")) ? $this->input->post('createduserid') : NULL);
            $this->db->set('createdUserName', (($this->input->post('createdusername') != "")) ? $this->input->post('createdusername') : NULL);
            $this->db->set('createdDateTime', (($this->input->post('createddate') != "")) ? $this->input->post('createddate') : NULL);

            $result = $this->db->insert('srp_erp_groupitemcategory');
            if ($result) {
                $this->session->set_flashdata('s', 'Sub Category Added Successfully');
                return true;
            }
            
        }
    }

    function edit_itemsubcategory()
    {
        $this->db->select('*');
        $this->db->where('itemCategoryID', $this->input->post('id'));
        return $this->db->get('srp_erp_groupitemcategory')->row_array();
    }

    function update_subsubcategory()
    {
        $prefix = $this->db->query("SELECT itemCategoryID,description FROM srp_erp_groupitemcategory WHERE groupID = " . current_companyID() . " AND codePrefix = '" . $this->input->post('codePrefix') . "' AND itemCategoryID != " . $this->input->post('subsubcatregoryeditfrm'))->row_array();

        if ($prefix)
        {
            $this->session->set_flashdata('e', 'Prefix already added');
            return false;
        } 

        $data['description'] = ((($this->input->post('descriptionsubsub') != "")) ? $this->input->post('descriptionsubsub') : NULL);
        $data['codePrefix'] = ((($this->input->post('codePrefix') != "")) ? $this->input->post('codePrefix') : NULL);

        $this->db->where('itemCategoryID', $this->input->post('subsubcatregoryeditfrm'));
        $result = $this->db->update('srp_erp_groupitemcategory', $data);
        if ($result) {
            $this->session->set_flashdata('s', 'Data Updated Successfully');
            return true;
        }else{
            $this->session->set_flashdata('s', 'No changes found');
            return true;
        }
    }

    function update_itemsubcategory()
    {
        $prefix = $this->db->query("SELECT itemCategoryID,description FROM srp_erp_groupitemcategory WHERE groupID = " . current_companyID() . " AND codePrefix = '" . $this->input->post('codePrefix') . "' AND itemCategoryID != " . $this->input->post('subcatregoryeditfrm'))->row_array();

        if ($prefix)
        {
            $this->session->set_flashdata('e', 'Prefix already added');
            return false;
        } 

        $data['description'] = ((($this->input->post('description') != "")) ? $this->input->post('description') : NULL);
        $data['codePrefix'] = ((($this->input->post('codePrefix') != "")) ? $this->input->post('codePrefix') : NULL);
        /* $data['revenueGL'] = ((($this->input->post('revnugledit') != "")) ? $this->input->post('revnugledit') : NULL);
        $data['costGL'] = ((($this->input->post('costgledit') != "")) ? $this->input->post('costgledit') : NULL);
        $data['assetGL'] = ((($this->input->post('assetgledit') != "")) ? $this->input->post('assetgledit') : NULL);*/

        $this->db->where('itemCategoryID', $this->input->post('subcatregoryeditfrm'));
        $result = $this->db->update('srp_erp_groupitemcategory', $data);
        if ($result) {
            $this->session->set_flashdata('s', 'Data Updated Successfully');
            return true;
        }else{
            $this->session->set_flashdata('s', 'No changes found');
            return true;
        }
    }

    function save_sub_sub_category()
    {
        $companyid = current_companyID();
        /*$this->db->select('companyGroupID');
        $this->db->where('companyID', $companyid);
        $grp = $this->db->get('srp_erp_companygroupdetails')->row_array();*/
        $grpid = $companyid;

        $prefix = $this->db->query("SELECT itemCategoryID,description FROM srp_erp_groupitemcategory WHERE groupID = " . $grpid . " AND codePrefix = '" . $this->input->post('codePrefix') . "'")->row_array();

        if ($prefix)
        {
            $this->session->set_flashdata('e', 'Prefix already added');
            return false;
        } 

        if (!$this->input->post('subsubedit')) {
            $this->db->set('masterID', (($this->input->post('subsubcategoryedit') != "")) ? $this->input->post('subsubcategoryedit') : NULL);
            $this->db->set('groupID', $grpid);

            $this->db->set('description', (($this->input->post('subsubcategory') != "")) ? $this->input->post('subsubcategory') : NULL);
            $this->db->set('codePrefix', (($this->input->post('codePrefix') != "")) ? $this->input->post('codePrefix') : NULL);
            /*$this->db->set('revenueGL', (($this->input->post('rvgl') != "")) ? $this->input->post('rvgl') : NULL);
            $this->db->set('costGL', (($this->input->post('cstgl') != "")) ? $this->input->post('cstgl') : NULL);
            $this->db->set('assetGL', (($this->input->post('astgl') != "")) ? $this->input->post('astgl') : NULL);*/
            $result = $this->db->insert('srp_erp_groupitemcategory');
            if ($result) {
                $this->session->set_flashdata('s', 'Sub Sub Category Added Successfully');
                return true;
            }else{
                $this->session->set_flashdata('s', 'Error Occurred');
                return false;
            }
        }
    }

    function edit_itemsubsubcategory()
    {
        $this->db->select('*');
        $this->db->where('itemCategoryID', $this->input->post('id'));
        return $this->db->get('srp_erp_groupitemcategory')->row_array();
    }

    function save_item_category_link()
    {
        $companyid = $this->input->post('companyIDgrp');
        $itemCategoryID = $this->input->post('itemCategoryID');
        $com = current_companyID();
        /*$this->db->select('companyGroupID');
        $this->db->where('companyID', $com);
        $grp = $this->db->get('srp_erp_companygroupdetails')->row_array();*/
        $grpid = $com;
        $masterGroupID=getParentgroupMasterID();
        $this->db->delete('srp_erp_groupitemcategorydetails', array('companyGroupID' => $grpid, 'groupItemCategoryID' => $this->input->post('itemCategoryIDhn')));
        foreach($companyid as $key => $val){
            $data['groupItemCategoryID'] = trim($this->input->post('itemCategoryIDhn') ?? '');
            $data['itemCategoryID'] = trim($itemCategoryID[$key]);
            $data['companyID'] = trim($val);
            $data['companyGroupID'] = $masterGroupID;

            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $results = $this->db->insert('srp_erp_groupitemcategorydetails', $data);
        }

        if ($results) {
            return array('s', 'Item Category Link Saved Successfully');
        } else {
            return array('e', 'Item Category Link Save Failed');
        }
    }

    function delete_item_category_link()
    {
        $this->db->where('groupItemCategoryDetailID', $this->input->post('groupItemCategoryDetailID'));
        $result = $this->db->delete('srp_erp_groupitemcategorydetails');
        return array('s', 'Record Deleted Successfully');
    }

    function load_category_header()
    {
        $this->db->select('*');
        //$this->db->join('srp_erp_groupcustomerdetails', 'srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID');
        $this->db->where('itemCategoryID', $this->input->post('groupItemCategoryID'));
        return $this->db->get('srp_erp_groupitemcategory')->row_array();
    }

    function save_sub_item_category_link()
    {
        $companyid = $this->input->post('companyIDgrp');
        $itemCategoryID = $this->input->post('itemCategoryID');
        $masterGroupID=getParentgroupMasterID();
        $com = current_companyID();
        $grpid = $com;
        $results=$this->db->delete('srp_erp_groupitemcategorydetails', array('companyGroupID' => $grpid, 'groupItemCategoryID' => $this->input->post('subitemCategoryIDhn')));
        foreach($companyid as $key => $val){
            $data['groupItemCategoryID'] = trim($this->input->post('subitemCategoryIDhn') ?? '');
            $data['itemCategoryID'] = trim($itemCategoryID[$key]);
            $data['companyID'] = trim($val);
            $data['companyGroupID'] = $masterGroupID;

            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];

            $results = $this->db->insert('srp_erp_groupitemcategorydetails', $data);
        }

        if ($results) {
            return array('s', 'Item Sub Category Link Saved Successfully');
        } else {
            return array('e', 'Item Sub Category Link Save Failed');
        }
    }

    function add_buyers_to_category(){
        $buyers_subsub = $this->input->post("buyers_for_cat");
        $selected_cat_id = $this->input->post("selected_cat_id");
        $type = $this->input->post("selected_cat_type");
        $groupCompanyID =current_companyID();
        $this->db->select('*');
        $this->db->where('groupID', current_companyID());
        $this->db->where('itemCategoryID', $selected_cat_id);
        $this->db->from('srp_erp_groupitemcategory');
        $itemCategory_arr= $this->db->get()->row_array();

        $buyers_subsub_str=NULL;
        if(count($buyers_subsub)>0){
            $buyers_subsub_str = implode(",",$buyers_subsub);
        }

        $companyList = $this->db->query(
            "SELECT companyID 
             FROM srp_erp_companygroupdetails 
             WHERE srp_erp_companygroupdetails.companyGroupID = $groupCompanyID"
            )->result_array();

        if($type==1){

            if(count($buyers_subsub)>0){
                foreach($buyers_subsub as $val){

                    $this->db->select('*');
                    $this->db->where('groupID', current_companyID());
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
                        $data_buyer['groupID'] = current_companyID();
                        $data_buyer['createdUserGroup'] = $this->common_data['user_group'];
                        $data_buyer['createdPCID'] = $this->common_data['current_pc'];
                        $data_buyer['createdUserID'] =  $this->common_data['current_userID'];
                        $data_buyer['createdDateTime'] = $this->common_data['current_date'];
                        $data_buyer['createdUserName'] = $this->common_data['current_user'];
                
                        $this->db->insert('srp_erp_incharge_assign', $data_buyer);
                        $last_id = $this->db->insert_id();
                        if(count( $companyList)>0){

                            foreach($companyList as $com){
                                $data_buyer_group_company['documentID'] = $itemCategory_arr['codePrefix'];
                                $data_buyer_group_company['catID'] =$itemCategory_arr['masterID'];
                                //$data_buyer['subCatID'] = $itemCategory_arr['masterID'];
                                $data_buyer_group_company['subCatID'] = $itemCategory_arr['itemCategoryID'];
                                $data_buyer_group_company['empID'] = $val;
                                $data_buyer_group_company['companyID'] = $com['companyID'];
                                $data_buyer_group_company['createdUserGroup'] = $this->common_data['user_group'];
                                $data_buyer_group_company['createdPCID'] = $this->common_data['current_pc'];
                                $data_buyer_group_company['createdUserID'] =  $this->common_data['current_userID'];
                                $data_buyer_group_company['createdDateTime'] = $this->common_data['current_date'];
                                $data_buyer_group_company['createdUserName'] = $this->common_data['current_user'];
                                $data_buyer_group_company['groupAssignMasterID'] = $last_id;
                        
                                $this->db->insert('srp_erp_incharge_assign', $data_buyer_group_company);
                            }

                        }
                    }
                }
            }

        }else{

            if(count($buyers_subsub)>0){
                foreach($buyers_subsub as $val){

                    $this->db->select('*');
                    $this->db->where('groupID', current_companyID());
                    $this->db->where('subSubCatID', $selected_cat_id);
                    $this->db->where('userType', 0);
                    $this->db->where('empID', $val);
                    $this->db->from('srp_erp_incharge_assign');
                    $added_emp_sub_sub= $this->db->get()->row_array();

                    if(!$added_emp_sub_sub){
                        $this->db->select('*');
                        $this->db->where('groupID', current_companyID());
                        $this->db->where('itemCategoryID', $itemCategory_arr['masterID']);
                        $this->db->from('srp_erp_groupitemcategory');
                        $itemCategory_arr_master= $this->db->get()->row_array();

                        $data_buyer['documentID'] = $itemCategory_arr['codePrefix'];
                        $data_buyer['catID'] =$itemCategory_arr_master['masterID'];
                        $data_buyer['subCatID'] = $itemCategory_arr['masterID'];
                        $data_buyer['subSubCatID'] = $itemCategory_arr['itemCategoryID'];
                        $data_buyer['empID'] = $val;
                        $data_buyer['groupID'] = current_companyID();
                        $data_buyer['createdUserGroup'] = $this->common_data['user_group'];
                        $data_buyer['createdPCID'] = $this->common_data['current_pc'];
                        $data_buyer['createdUserID'] =  $this->common_data['current_userID'];
                        $data_buyer['createdDateTime'] = $this->common_data['current_date'];
                        $data_buyer['createdUserName'] = $this->common_data['current_user'];
                
                        $this->db->insert('srp_erp_incharge_assign', $data_buyer);

                        $last_id = $this->db->insert_id();
                        if(count( $companyList)>0){

                            foreach($companyList as $com){

                                $data_buyer_com['documentID'] = $itemCategory_arr['codePrefix'];
                                $data_buyer_com['catID'] =$itemCategory_arr_master['masterID'];
                                $data_buyer_com['subCatID'] = $itemCategory_arr['masterID'];
                                $data_buyer_com['subSubCatID'] = $itemCategory_arr['itemCategoryID'];
                                $data_buyer_com['empID'] = $val;
                                $data_buyer_com['companyID'] = $com['companyID'];
                                $data_buyer_com['createdUserGroup'] = $this->common_data['user_group'];
                                $data_buyer_com['createdPCID'] = $this->common_data['current_pc'];
                                $data_buyer_com['createdUserID'] =  $this->common_data['current_userID'];
                                $data_buyer_com['createdDateTime'] = $this->common_data['current_date'];
                                $data_buyer_com['createdUserName'] = $this->common_data['current_user'];
                                $data_buyer_com['groupAssignMasterID'] = $last_id;
                        
                                $this->db->insert('srp_erp_incharge_assign', $data_buyer_com);

                            }
                        }
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

    function delete_category_assign_buyers_group(){
        $id = $this->input->post('id');

        $this->db->trans_start();

        $this->db->select('*');
        $this->db->where('assignMasterID', $id);
        $this->db->from('srp_erp_incharge_assign');
        $assign= $this->db->get()->result_array();

        if(!empty($assign)){
            return array('w','Buyer Already Assigned');
        }else{

            $this->db->where('groupAssignMasterID', $id)->delete('srp_erp_incharge_assign');
            $this->db->where('autoID', $id)->delete('srp_erp_incharge_assign');
        }
    
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return array('e','Update Fail');
        } else {
            
            $this->db->trans_commit();
            return array('s','Successfully Deleted!');
        }
    }

}