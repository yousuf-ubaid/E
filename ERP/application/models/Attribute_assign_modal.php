<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Attribute_assign_modal extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
    }

    function load_attributes(){
        $this->db->select('*');
        $this->db->from('srp_erp_systemattributemaster');
        $data['attributes'] = $this->db->get()->result_array();

        $this->db->select('*');
        $this->db->from('srp_erp_companyattributeassign');
        $this->db->where('companyID',current_companyID());
        $data['attributAssign'] = $this->db->get()->result_array();

        return $data;
    }

    function save_assigned_attributes(){
        $systemAttributeID=$this->input->post('systemAttributeID');
        $companyAttributeID=$this->input->post('companyAttributeID');
        $default=$this->input->post('default');
        $isMandatory=$this->input->post('isMandatory');

        foreach($systemAttributeID as $key => $val){
            if(!empty($companyAttributeID[$key]) && $default[$key]==1){
                $data['systemAttributeID'] = $val;
                $data['isMandatory'] = $isMandatory[$key];
                $data['modifiedPCID'] = $this->common_data['current_pc'];
                $data['modifiedUserID'] = $this->common_data['current_userID'];
                $data['modifiedDateTime'] = $this->common_data['current_date'];
                $data['modifiedUserName'] = $this->common_data['current_user'];
                $this->db->where('companyAttributeID', $companyAttributeID[$key]);
                $this->db->update('srp_erp_companyattributeassign', $data);
            }else if(!empty($companyAttributeID[$key]) && $default[$key]==0){
                $this->db->delete('srp_erp_companyattributeassign', array('companyAttributeID' => $companyAttributeID[$key]));
            }else{
                if($default[$key]==1){
                    $dat['systemAttributeID'] = $val;
                    $dat['isMandatory'] = $isMandatory[$key];
                    $dat['companyID'] = current_companyID();

                    $dat['createdPCID'] = $this->common_data['current_pc'];
                    $dat['createdUserID'] = $this->common_data['current_userID'];
                    $dat['createdDateTime'] = $this->common_data['current_date'];
                    $dat['createdUserName'] = $this->common_data['current_user'];
                    $this->db->insert('srp_erp_companyattributeassign', $dat);
                }
            }
        }
    }

    function load_attributes_edit(){
        $companyAttributeID=$this->input->post('companyAttributeID');
        $this->db->select('srp_erp_companyattributeassign.*,srp_erp_systemattributemaster.attributeDescription as attributeDescription');
        $this->db->from('srp_erp_companyattributeassign');
        $this->db->join('srp_erp_systemattributemaster', 'srp_erp_systemattributemaster.systemAttributeID = srp_erp_companyattributeassign.systemAttributeID');
        $this->db->where('companyID',current_companyID());
        $this->db->where('companyAttributeID',$companyAttributeID);
        $data['attributAssign'] = $this->db->get()->row_array();

        return $data;
    }


    function update_assigned_attributes(){
        $systemAttributeID=$this->input->post('systemAttributeID');
        $companyAttributeID=$this->input->post('companyAttributeID');
        $default=$this->input->post('default');
        $isMandatory=$this->input->post('isMandatory');

        if(!empty($companyAttributeID) && $default==1){
            $data['systemAttributeID'] = $systemAttributeID;
            $data['isMandatory'] = $isMandatory;
            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $data['modifiedUserName'] = $this->common_data['current_user'];
            $this->db->where('companyAttributeID', $companyAttributeID);
            $this->db->update('srp_erp_companyattributeassign', $data);
        }else if(!empty($companyAttributeID) && $default==0){
            $this->db->delete('srp_erp_companyattributeassign', array('companyAttributeID' => $companyAttributeID));
        }

    }

    function delete_attribute(){
        $companyAttributeID=$this->input->post('companyAttributeID');
       $result= $this->db->delete('srp_erp_companyattributeassign', array('companyAttributeID' => $companyAttributeID));
        if($result){
            return array('s','Attribute deleted successfully');
        }
    }



}
