<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Terms_and_condition_modal extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
    }


    function save_notes(){
        $documentID=$this->input->post('documentID');
        $description=$this->input->post('description');
        $isDefault=$this->input->post('isDefault');
        $autoIDhn=$this->input->post('autoIDhn');

        if($documentID=='PO'){
            $poType=$this->input->post('poType');
            $data['typeID'] = $poType;
        }
        if(empty($autoIDhn)){
            $this->db->select('autoID,isDefault');
            $this->db->from('srp_erp_termsandconditions');
            $this->db->where('companyID',current_companyID());
            $this->db->where('documentID',$documentID);

            if($documentID=='PO'){
                $this->db->where('typeID',$poType);
            }
            
            $defexsist = $this->db->get()->result_array();

            if(!empty($defexsist)){
                foreach($defexsist as $val){
                    if($val['isDefault']==1 && $isDefault==1){
                        $datas['isDefault'] = 0;
                        $this->db->where('autoID', $val['autoID']);
                        $this->db->update('srp_erp_termsandconditions', $datas);
                    }
                }
            }

            $data['documentID'] = $documentID;
            $data['description'] = $description;
            $data['isDefault'] = $isDefault;
            $data['companyID'] = current_companyID();

            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $result=$this->db->insert('srp_erp_termsandconditions', $data);
            if($result){
                return array('s','Saved successfully');
            }else{
                return array('e','Save failed');
            }
        }else{
            $this->db->select('autoID,isDefault');
            $this->db->from('srp_erp_termsandconditions');
            $this->db->where('companyID',current_companyID());
            $this->db->where('documentID',$documentID);

            if($documentID=='PO'){
                $this->db->where('typeID',$poType);
            }

            $defexsist = $this->db->get()->result_array();
            if(!empty($defexsist)){
                foreach($defexsist as $val){
                    if($val['isDefault']==1 && $isDefault==1){

                        $datas['isDefault'] = 0;
                        $this->db->where('autoID', $val['autoID']);
                        $this->db->update('srp_erp_termsandconditions', $datas);
                        
                    }
                }
            }


            $data['documentID'] = $documentID;
            $data['description'] = $description;
            $data['isDefault'] = $isDefault;
            $data['companyID'] = current_companyID();

            $data['modifiedPCID'] = $this->common_data['current_pc'];
            $data['modifiedUserID'] = $this->common_data['current_userID'];
            $data['modifiedDateTime'] = $this->common_data['current_date'];
            $data['modifiedUserName'] = $this->common_data['current_user'];

            $this->db->where('autoID', $autoIDhn);
            $result=$this->db->update('srp_erp_termsandconditions', $data);
            if($result){
                return array('s','Updated successfully');
            }else{
                return array('e','Update failed');
            }
        }

    }

    function get_notes_edit(){
        $autoID=$this->input->post('autoID');
        $this->db->select('*');
        $this->db->from('srp_erp_termsandconditions');
        $this->db->where('companyID',current_companyID());
        $this->db->where('autoID',$autoID);
        $data = $this->db->get()->row_array();

        return $data;
    }


    function delete_notes(){
        $autoID=$this->input->post('autoID');
        $this->db->select('autoID');
        $this->db->from('srp_erp_termsandconditions');
        $this->db->where('companyID',current_companyID());
        $this->db->where('autoID',$autoID);
        $this->db->where('isDefault',1);
        $defexsist = $this->db->get()->row_array();
        if(empty($defexsist)){
            $result= $this->db->delete('srp_erp_termsandconditions', array('autoID' => $autoID));
            if($result){
                return array('s','Deleted successfully');
            }
        }else{
            return array('w','This record is set as default and cannot be deleted');
        }

    }

    function change_isDefault(){

        $isDefault=$this->input->post('isdefault');
        $autoIDhn=$this->input->post('autoID');
        $def=1;
        if($isDefault==1){
            $def=0;
        }

        $this->db->select('documentID,typeID');
        $this->db->from('srp_erp_termsandconditions');
        $this->db->where('companyID',current_companyID());
        $this->db->where('autoID',$autoIDhn);
        $docID = $this->db->get()->row_array();

        if($docID['documentID']=='PO'){
            $datas['isDefault'] = 0;
            $this->db->where('documentID', $docID['documentID']);
            $this->db->where('typeID', $docID['typeID']);
            $this->db->where('companyID',current_companyID());
            $this->db->update('srp_erp_termsandconditions', $datas);

            $data['isDefault'] = $def;
            $this->db->where('autoID', $autoIDhn);
            $result=$this->db->update('srp_erp_termsandconditions', $data);
        }else{
            $datas['isDefault'] = 0;
            $this->db->where('documentID', $docID['documentID']);
            $this->db->where('companyID',current_companyID());
            $this->db->update('srp_erp_termsandconditions', $datas);
    
            $data['isDefault'] = $def;
            $this->db->where('autoID', $autoIDhn);
            $result=$this->db->update('srp_erp_termsandconditions', $data);
        }
        
        if($result){
            return array('s','Updated successfully');
        }else{
            return array('e','Update failed');
        }

    }



}
