<?php
class Group_uom_model extends ERP_Model
{

    function save_uom()
    {
        $this->db->trans_start();
        $companyID = $this->common_data['company_data']['company_id'];
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();

        $data['UnitShortCode'] = trim($this->input->post('UnitShortCode') ?? '');
        $data['UnitDes'] = trim($this->input->post('UnitDes') ?? '');
        $data['modifiedPCID'] = $this->common_data['current_pc'];
        $data['modifiedUserID'] = $this->common_data['current_userID'];
        $data['modifiedUserName'] = $this->common_data['current_user'];
        $data['modifiedDateTime'] = $this->common_data['current_date'];
        if (trim($this->input->post('UnitID') ?? ''))
        {
            $this->db->where('UnitID', trim($this->input->post('UnitID') ?? ''));
            $this->db->update('srp_erp_group_unit_of_measure', $data);
            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->session->set_flashdata('e', 'Unit of measure Update Failed ');
                return array('status' => false);
            }
            else
            {
                $this->session->set_flashdata('s', 'Unit of measure Updated Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $this->input->post('UnitID'));
            }
        }
        else
        {
            $data['groupID'] = $companyID;
            //$data['createdUserGroup'] = $this->common_data['user_group'];
            $data['createdPCID'] = $this->common_data['current_pc'];
            $data['createdUserID'] = $this->common_data['current_userID'];
            $data['createdUserName'] = $this->common_data['current_user'];
            $data['createdDateTime'] = $this->common_data['current_date'];
            $this->db->insert('srp_erp_group_unit_of_measure', $data);
            $last_id = $this->db->insert_id();
            $this->db->insert('srp_erp_group_unitsconversion', array('masterUnitID' => $last_id, 'subUnitID' => $last_id, 'conversion' => 1, 'timestamp' => date('Y-m-d'), 'groupID' => $companyID));

            $this->db->trans_complete();
            if ($this->db->trans_status() === FALSE)
            {
                $this->session->set_flashdata('e', 'Unit of measure Save Failed ');
                return array('status' => false);
            }
            else
            {
                $this->session->set_flashdata('s', 'Unit of measure Saved Successfully.');
                $this->db->trans_commit();
                return array('status' => true, 'last_id' => $last_id);
            }
        }
    }

    function fetch_convertion_detail_table()
    {
        $companyID = $this->common_data['company_data']['company_id'];
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();

        $this->db->select('subUnitID,conversion,s.UnitShortCode as sub_code,s.UnitDes as sub_dese,m.UnitShortCode as m_code,m.UnitDes as m_dese');
        $this->db->where('masterUnitID', trim($this->input->post('masterUnitID') ?? ''));
        $this->db->where('srp_erp_group_unitsconversion.groupID', $companyID);
        $this->db->from('srp_erp_group_unitsconversion');
        $this->db->join('srp_erp_group_unit_of_measure s', 's.UnitID = srp_erp_group_unitsconversion.subUnitID');
        $this->db->join('srp_erp_group_unit_of_measure m', 'm.UnitID = srp_erp_group_unitsconversion.masterUnitID');
        $data['detail'] = $this->db->get()->result_array();

        $this->db->select('UnitID,UnitShortCode,UnitDes');
        $this->db->where('UnitID !=', trim($this->input->post('masterUnitID') ?? ''));
        $this->db->where('srp_erp_group_unit_of_measure.groupID', $companyID);
        $this->db->from('srp_erp_group_unit_of_measure');
        $data['drop'] = $this->db->get()->result_array();
        return $data;
    }

    function change_conversion()
    {
        $this->db->trans_start();
        $companyID = $this->common_data['company_data']['company_id'];
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();

        $data['masterUnitID'] = trim($this->input->post('masterUnitID') ?? '');
        $data['subUnitID'] = trim($this->input->post('subUnitID') ?? '');
        $data['conversion'] = round($this->input->post('conversion'), 20);
        $data['groupID'] = $companyID;

        $this->db->where('masterUnitID', $data['masterUnitID']);
        $this->db->where('subUnitID', $data['subUnitID']);
        $this->db->update('srp_erp_group_unitsconversion', $data);

        $data['subUnitID'] = trim($this->input->post('masterUnitID') ?? '');
        $data['masterUnitID'] = trim($this->input->post('subUnitID') ?? '');
        $data['conversion'] = round((1 / $this->input->post('conversion')), 20);
        $data['groupID'] = $companyID;

        $this->db->where('masterUnitID', $data['masterUnitID']);
        $this->db->where('subUnitID', $data['subUnitID']);
        $this->db->update('srp_erp_group_unitsconversion', $data);

        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->session->set_flashdata('e', 'Unit of measure conversion Update Failed ');
            return array('status' => false);
        }
        else
        {
            $this->session->set_flashdata('s', 'Unit of measure conversion Updated Successfully.');
            $this->db->trans_commit();
            return array('status' => true);
        }
    }

    function save_uom_conversion()
    {
        //$this->db->trans_start();
        $companyID = $this->common_data['company_data']['company_id'];
        //$companyGroup = $this->db->query("SELECT companyGroupID FROM srp_erp_companygroupdetails WHERE srp_erp_companygroupdetails.companyID = {$companyID}")->row_array();

        $data['masterUnitID'] = trim($this->input->post('masterUnitID') ?? '');
        $data['subUnitID'] = trim($this->input->post('subUnitID') ?? '');
        $data['conversion'] = round($this->input->post('conversion'), 20);
        $data['groupID'] = $companyID;

        $this->db->insert('srp_erp_group_unitsconversion', $data);
        $last_id = $this->db->insert_id();

        $data['subUnitID'] = trim($this->input->post('masterUnitID') ?? '');
        $data['masterUnitID'] = trim($this->input->post('subUnitID') ?? '');
        $data['conversion'] = round((1 / $this->input->post('conversion')), 20);
        $data['groupID'] = $companyID;
        $this->db->insert('srp_erp_group_unitsconversion', $data);
        //$this->db->trans_complete();
        if ($this->db->trans_status() === FALSE)
        {
            $this->session->set_flashdata('e', 'Unit of measure conversion Save Failed ');
            return array('status' => false);
        }
        else
        {
            $this->session->set_flashdata('s', 'Unit of measure conversion Saved Successfully.');
            $this->db->trans_commit();
            return array('status' => true, 'last_id' => $last_id);
        }
    }

    function delete_uom_link()
    {
        $this->db->where('groupUOMDetailID', $this->input->post('groupUOMDetailID'));
        $result = $this->db->delete('srp_erp_groupuomdetails');
        return array('s', 'Record Deleted Successfully');
    }

    function save_uom_link()
    {
        $companyid = $this->input->post('companyIDgrp');
        $UOMMasterID = $this->input->post('UOMMasterID');
        $com = current_companyID();
        /*$this->db->select('companyGroupID');
        $this->db->where('companyID', $com);
        $grp = $this->db->get('srp_erp_companygroupdetails')->row_array();*/
        $grpid = $com;

        $results = $this->db->delete('srp_erp_groupuomdetails', array('companyGroupID' => $grpid, 'groupUOMMasterID' => $this->input->post('groupUOMMasterID')));

        foreach ($companyid as $key => $val)
        {
            if (!empty($UOMMasterID[$key]))
            {
                $data['groupUOMMasterID'] = trim($this->input->post('groupUOMMasterID') ?? '');
                $data['UOMMasterID'] = trim($UOMMasterID[$key]);
                $data['companyID'] = trim($val);
                $data['companyGroupID'] = $grpid;

                $data['createdPCID'] = $this->common_data['current_pc'];
                $data['createdUserID'] = $this->common_data['current_userID'];
                $data['createdUserName'] = $this->common_data['current_user'];
                $data['createdDateTime'] = $this->common_data['current_date'];

                $results = $this->db->insert('srp_erp_groupuomdetails', $data);
            }
            //$last_id = $this->db->insert_id();
        }

        if ($results)
        {
            return array('s', 'UOM Link Saved Successfully');
        }
        else
        {
            return array('e', 'UOM Link Save Failed');
        }
    }

    function load_uom_header()
    {
        $this->db->select('*');
        //$this->db->join('srp_erp_groupcustomerdetails', 'srp_erp_groupcustomermaster.groupCustomerAutoID = srp_erp_groupcustomerdetails.groupCustomerMasterID');
        $this->db->where('UnitID', $this->input->post('groupUOMMasterID'));
        return $this->db->get('srp_erp_group_unit_of_measure')->row_array();
    }

    function fetch_uom_details()
    {
        $groupUomID =  trim($this->input->post('groupUomID') ?? '');
        $data['uomDetails'] = $this->db->query("SELECT 
                *
                FROM 
                srp_erp_group_unit_of_measure 

                where 
                UnitID = $groupUomID ")->row_array();
        return $data;
    }

    function save_uom_duplicate()
    {
        $companyid = $this->input->post('checkedCompanies');
        $com = current_companyID();
        $grpid = $com;
        $masterGroupID = getParentgroupMasterID();
        $results = '';
        $comparr = array();

        foreach ($companyid as $val)
        {
            $i = 0;
            $this->db->select('groupUOMDetailID');
            $this->db->where('groupUOMMasterID', $this->input->post('uomIdDuplicatehn'));
            $this->db->where('companyID', $val);
            $this->db->where('companyGroupID', $grpid);
            $linkexsist = $this->db->get('srp_erp_groupuomdetails')->row_array();

            $this->db->select('*');
            $this->db->where('UnitID', $this->input->post('uomIdDuplicatehn'));
            $CurrentCus = $this->db->get('srp_erp_group_unit_of_measure')->row_array();


            $this->db->select('UnitID');
            $this->db->where('UnitShortCode', $CurrentCus['UnitShortCode']);
            $this->db->where('companyID', $val);

            $CurrentUoMexsist = $this->db->get('srp_erp_unit_of_measure')->row_array();

            if (!empty($CurrentUoMexsist))
            {
                $i++;
                $companyName = get_companyData($val);

                array_push($comparr, array("companyname" => $companyName['company_name'], "message" => "Unit of Measure already exist" . " (" . $CurrentCus['UnitDes'] . ")"));
            }

            if ($i == 0)
            {
                if (empty($linkexsist))
                {

                    $data['UnitShortCode'] = $CurrentCus['UnitShortCode'];
                    $data['UnitDes'] = $CurrentCus['UnitDes'];
                    $data['companyID'] = $val;
                    // $companyCode = get_companyData($val);
                    // $data['companyCode'] = $companyCode['company_code'];
                    $data['createdPCID'] = $this->common_data['current_pc'];
                    $data['createdUserID'] = $this->common_data['current_userID'];
                    $data['createdDateTime'] = $this->common_data['current_date'];
                    $data['createdUserName'] = $this->common_data['current_user'];
                    $this->db->insert('srp_erp_unit_of_measure', $data);
                    $last_id = $this->db->insert_id();


                    $dataLink['groupUOMMasterID'] = trim($this->input->post('uomIdDuplicatehn') ?? '');
                    $dataLink['UOMMasterID'] = trim($last_id);
                    $dataLink['companyID'] = trim($val);
                    $dataLink['companyGroupID'] = $masterGroupID;
                    $dataLink['createdPCID'] = $this->common_data['current_pc'];
                    $dataLink['createdUserID'] = $this->common_data['current_userID'];
                    $dataLink['createdUserName'] = $this->common_data['current_user'];
                    $dataLink['createdDateTime'] = $this->common_data['current_date'];

                    $results = $this->db->insert('srp_erp_groupuomdetails', $dataLink);
                }
            }
            else
            {
                continue;
            }
        }

        if ($results)
        {
            return array('s', 'Unit of Measurement Replicated Successfully', $comparr);
        }
        else
        {
            return array('e', 'Unit of Measurement Replication not successful', $comparr);
        }
    }
}
