<?php

class MFQ_warehouse_model extends ERP_Model
{
    function add_warehouse()
    {
        $result = $this->db->query('INSERT INTO srp_erp_mfq_warehousemaster ( warehouseAutoID, warehouseCode, warehouseDescription, warehouseLocation, warehouseTel,warehouseAddress,companyID) 
                                SELECT wareHouseAutoID, wareHouseCode, wareHouseDescription, wareHouseLocation, warehouseTel,warehouseAddress,companyID FROM srp_erp_warehousemaster 
                                WHERE companyID = ' . current_companyID() . '  AND warehouseAutoID IN(' . join(",", $this->input->post('selectedItemsSync')) . ')');

        if ($result) {
            return array('error' => 0, 'message' => 'Record added updated');
        }
        else{
            return array('error' => 1, 'message' => 'Record adding failed');
        }
    }

    function save_warehouse()
    {
        if (empty($this->input->post('mfqWarehouseAutoID'))) {
            $companyID = current_companyID();
            $warehousecode = $this->input->post('warehouseCode');
            $Warehouse = $this->db->query("SELECT mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster where companyID = {$companyID} AND warehouseCode = '{$warehousecode}'")->row_array();
            if ($Warehouse) {
                return array('error' => 1, 'message' => 'Warehouse already created');
            } else {
                $this->db->set('warehouseCode', (($this->input->post('warehouseCode') != "")) ? $this->input->post('warehouseCode') : NULL);
                $this->db->set('warehouseDescription', (($this->input->post('warehouseDescription') != "")) ? $this->input->post('warehouseDescription') : NULL);
                $this->db->set('warehouseLocation', (($this->input->post('warehouseLocation') != "")) ? $this->input->post('warehouseLocation') : NULL);
                $this->db->set('warehouseAddress', (($this->input->post('warehouseAddress') != "")) ? $this->input->post('warehouseAddress') : NULL);
                $this->db->set('warehouseTel', (($this->input->post('warehouseTel') != "")) ? $this->input->post('warehouseTel') : NULL);
                $this->db->set('isFromERP', 0);
                $this->db->set('createdUserGroup', ($this->common_data['user_group']));
                $this->db->set('createdPCID', ($this->common_data['current_pc']));
                $this->db->set('createdUserID', ($this->common_data['current_userID']));
                $this->db->set('createdDateTime', ($this->common_data['current_date']));
                $this->db->set('createdUserName', ($this->common_data['current_user']));
                $this->db->set('companyID', ($this->common_data['company_data']['company_id']));
                $this->db->set('companyCode', ($this->common_data['company_data']['company_code']));
                $result = $this->db->insert('srp_erp_mfq_warehousemaster');
                if ($result) {
                    return array('error' => 0, 'message' => 'Warehouse successfully Added','code'=> 1);
                }
            }
        } else {
            $companyID = current_companyID();
            $warehousecode = $this->input->post('warehouseCode');
            $mfqWarehouseAutoID = $this->input->post('mfqWarehouseAutoID');
            $Warehouse = $this->db->query("SELECT mfqWarehouseAutoID FROM srp_erp_mfq_warehousemaster where companyID = {$companyID} AND warehouseCode = '{$warehousecode}' AND mfqWarehouseAutoID !=  $mfqWarehouseAutoID ")->row_array();
            if ($Warehouse) {
                return array('error' => 1, 'message' => 'Warehouse already created');
            } else {
                $data['warehouseCode'] = ((($this->input->post('warehouseCode') != "")) ? $this->input->post('warehouseCode') : NULL);
                $data['warehouseDescription'] = ((($this->input->post('warehouseDescription') != "")) ? $this->input->post('warehouseDescription') : NULL);
                $data['warehouseLocation'] = ((($this->input->post('warehouseLocation') != "")) ? $this->input->post('warehouseLocation') : NULL);
                $data['warehouseAddress'] = ((($this->input->post('warehouseAddress') != "")) ? $this->input->post('warehouseAddress') : NULL);
                $data['warehouseTel'] = ((($this->input->post('warehouseTel') != "")) ? $this->input->post('warehouseTel') : NULL);
                $data['modifiedPCID'] = ($this->common_data['current_pc']);
                $data['modifiedUserID'] = ($this->common_data['current_userID']);
                $data['modifiedDateTime'] = ($this->common_data['current_date']);
                $data['modifiedUserName'] = ($this->common_data['current_user']);
                $this->db->where('mfqWarehouseAutoID', $mfqWarehouseAutoID);
                $result = $this->db->update('srp_erp_mfq_warehousemaster', $data);
                if ($result) {
                    return array('error' => 0, 'message' => 'Warehouse successfully updated','code'=> 0);
                }
            }
        }
    }

    function get_warehouse()
    {
        $mfqWarehouseAutoID = $this->input->post('mfqWarehouseAutoID');
        $this->db->select('*');
        $this->db->from('srp_erp_mfq_warehousemaster');
        $this->db->where('mfqWarehouseAutoID', $mfqWarehouseAutoID);
        $result = $this->db->get()->row_array();
        return $result;
    }

    function link_warehouse()
    {
        $itemFromErp = $this->db->query('SELECT
                                 wareHouseAutoID, wareHouseCode, wareHouseDescription, wareHouseLocation, warehouseTel,warehouseAddress
                                FROM
                                    srp_erp_warehousemaster WHERE companyID = ' . $this->common_data['company_data']['company_id'] . ' AND wareHouseAutoID = ' . $this->input->post('selectedItemsSync'))->row_array();
        $result="";
        if ($itemFromErp) {
            $this->db->set('warehouseAutoID', $itemFromErp["wareHouseAutoID"]);
            $this->db->where('mfqWarehouseAutoID', $this->input->post('mfqWarehouseAutoID'));
            $result = $this->db->update('srp_erp_mfq_warehousemaster');
        }

        if ($result) {
            return array('error' => 0, 'message' => 'Warehouse successfully updated');
        }
        else{
            return array('error' => 1, 'message' => 'Record adding failed');
        }
    }

}
