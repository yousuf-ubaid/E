<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Group_management_model extends ERP_Model
{

    function __contruct()
    {
        parent::__contruct();
    }

    function save_sub_group()
    {
        $this->db->trans_start();
        $description = $this->input->post('description');
        $companyID = current_companyID();
        /*$this->db->select('companyGroupID');
        $this->db->where('companyID', $companyID);
        $grp= $this->db->get('srp_erp_companygroupdetails')->row_array();*/
        $companyGroupID=$this->input->post('companyGroupID');
        //$companyGroupID = $this->input->post('companyGroupID');
        $this->db->select('description');
        $this->db->where('description', trim($description));
        $this->db->where('companyGroupID', trim($companyGroupID));
        $this->db->from('srp_erp_companysubgroupmaster');
        $result=$this->db->get()->row_array();
        if($result){
            return array('e', 'Sub Group Already Exist');
        }else{
            $data['description'] = $description;
            $data['companyGroupID'] = $companyGroupID;

            $this->db->insert('srp_erp_companysubgroupmaster', $data);
            $last_id = $this->db->insert_id();
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE) {
                return array('e', 'Sub Group Save Failed');
            } else {
                return array('s', 'Sub Group Saved Successfully');
            }
        }

    }

    function load_company_sub_group(){
        $this->db->select('*');
        $this->db->where('companySubGroupID', trim($this->input->post('companySubGroupID') ?? ''));
        $this->db->from('srp_erp_companysubgroupmaster');
        return $this->db->get()->row_array();
    }

    function edit_sub_group(){

        $descriptionedit = $this->input->post('descriptionedit');
        $companySubGroupID = $this->input->post('companySubGroupID');
        $companyID = current_companyID();
        /*$this->db->select('companyGroupID');
        $this->db->where('companyID', $companyID);
        $grp= $this->db->get('srp_erp_companygroupdetails')->row_array();*/
        $grpid=$companyID;

        $this->db->select('description');
        $this->db->where('description', trim($descriptionedit));
        $this->db->where('companyGroupID', trim($grpid));
        $this->db->where('companySubGroupID !=', trim($companySubGroupID));
        $this->db->from('srp_erp_companysubgroupmaster');
        $result=$this->db->get()->row_array();

        if($result){
            return array('e', 'Sub Group Already Exist');
        }else{
            $datas = array(
                'description' => $descriptionedit,
            );
            $this->db->where('companySubGroupID', trim($companySubGroupID));
            $update = $this->db->update('srp_erp_companysubgroupmaster', $datas);
            if ($update || $update='') {
                return array('s', 'Sub Group Updated Successfully');
            } else {
                return array('e', 'Sub Group Updating Failed');
            }
        }
    }

}
