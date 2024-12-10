<?php
/**
 *
 * -- =============================================
 * -- File Name : Pos_config.php
 * -- Project Name : POS
 * -- Module Name : POS Config model
 * -- Create date : 13 October 2016
 * -- Description : database script related to pos config.
 *
 * --REVISION HISTORY
 * --Date: 13-Oct 2016 : file created
 * -- =============================================
 **/
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Mfq_master_model extends ERP_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function save_itemCategory($data = null)
    {
        $cur_dataTime = format_date_mysql_datetime();
        $data['companyID'] = current_companyID();
        $data['createdPCID'] = current_pc();
        $data['createdUserID'] = current_userID();
        $data['createdUserName'] = current_user();
        $data['createdDateTime'] = $cur_dataTime;
        $data['timestamp'] = $cur_dataTime;

        $result = $this->db->insert('srp_erp_mfq_category', $data);

        if ($result) {
            return array('error' => 0, 'message' => 'Category added successfully.');
        } else {
            return array('error' => 1, 'message' => 'Error while adding category !');
        }
    }

    function load_mfq_category_all($categoryType = 1)
    {
        $this->db->select('itemCategoryID as id,description as name, masterID as parentid, levelNo');
        $this->db->from('srp_erp_mfq_category');
        $this->db->where('categoryType', $categoryType);
        $this->db->where('companyID', current_companyID());
        $result = $this->db->get()->result_array();
        return $result;
    }
    function update_itemCategory()
    {
        $discripion = $this->input->post('description');
        $this->db->set('description',$discripion);
        $this->db->where('itemCategoryID', $this->input->post('masterID'));
        $update = $this->db->update('srp_erp_mfq_category');
        if ($update) {
            return array('error' => 0, 'message' => 'Category Update successfully.');
        } else {
            return array('error' => 1, 'message' => 'Error while Updating category !');
        }

    }

}