<?php

class MFQ_crew_model extends ERP_Model
{

    function add_crews()
    {
        $result = $this->db->query('INSERT INTO srp_erp_mfq_crews ( EIdNo, serialNo, ECode, EmpSecondaryCode, EmpTitleId, EmpDesignationId, Ename1, Ename2, Ename3, Ename4, initial, EmpShortCode, EmpImage, Gender, EpAddress1, EpAddress2, EpAddress3, EpAddress4, ZipCode, EpTelephone, EpFax, EcAddress1, EcAddress2, EcAddress3, EcAddress4, EcMobile, EEmail, EDOB, NIC, EPassportNO, EPassportExpiryDate, EVisaExpiryDate, MaritialStatus, Nationality, CreatedBy, CreatedDate, CreatedPC, `Timestamp`, Erp_companyID )
                                SELECT EIdNo, serialNo, ECode, EmpSecondaryCode, EmpTitleId, EmpDesignationId, Ename1, Ename2, Ename3, Ename4, initial, EmpShortCode, EmpImage, Gender, EpAddress1, EpAddress2, EpAddress3, EpAddress4, ZipCode, EpTelephone, EpFax, EcAddress1, EcAddress2, EcAddress3, EcAddress4, EcMobile, EEmail, EDOB, NIC, EPassportNO, EPassportExpiryDate, EVisaExpiryDate, MaritialStatus, Nationality, "' . current_userID() . '", CreatedDate, CreatedPC, `Timestamp`, Erp_companyID FROM srp_employeesdetails
                                WHERE Erp_companyID = ' . current_companyID() . '  AND EIdNo IN(' . join(",", $this->input->post('selectedItemsSync')) . ')');

        if ($result) {
            $this->session->set_flashdata('s', 'Records added Successfully');
            return array('status' => true);
        }
    }

    function insert_crew()
    {

        $this->db->select('*');
        $this->db->from('srp_erp_mfq_crews');
        $this->db->where('EEmail', $this->input->post('EEmail'));
        $this->db->where('Erp_companyID', current_companyID());
        $crew = $this->db->get()->row_array();

        if (!$crew) {
            $post = $this->input->post();
            unset($post['crewID']);

            $datetime = format_date_mysql_datetime();
            $post['Erp_companyID'] = current_companyID();
            $post['CreatedBy'] = current_userID();
            $post['CreatedDate'] = $datetime;
            $post['CreatedPC'] = current_pc();
            $post['Timestamp'] = $datetime;
            $post['isFromERP'] = 0;

            $result = $this->db->insert('srp_erp_mfq_crews', $post);
            if ($result) {
                return array('error' => 0, 'message' => 'Crew successfully Added', 'code' => 1);
            } else {
                return array('error' => 1, 'message' => 'Code: ' . $this->db->_error_number() . ' <br/>Message: ' . $this->db->_error_message());
            }
        } else {
            return array('error' => 1, 'message' => 'This email address is already added to this company');
        }
    }

    function get_srp_erp_mfq_crews()
    {
        $crewID = $this->input->post('crewID');
        $this->db->select('*');
        $this->db->from('srp_erp_mfq_crews');
        $this->db->where('crewID', $crewID);
        $result = $this->db->get()->row_array();
        return $result;
    }


    function update_crew()
    {
        $crewID = $this->input->post('crewID');
        $post = $this->input->post();
        unset($post['crewID']);
        $this->db->where('crewID', $crewID);
        $result = $this->db->update('srp_erp_mfq_crews', $post);
        if ($result) {
            return array('error' => 0, 'message' => 'Crew updated successfully', 'code' => 2);
        } else {
            return array('error' => 1, 'message' => 'Code: ' . $this->db->_error_number() . ' <br/>Message: ' . $this->db->_error_message());
        }

    }

    function link_crew()
    {

        $result = $this->db->query("SELECT EIdNo, serialNo, ECode, EmpSecondaryCode, EmpTitleId, EmpDesignationId, Ename1, Ename2, Ename3, Ename4, initial, EmpShortCode, EmpImage, Gender,
 EpAddress1, EpAddress2, EpAddress3, EpAddress4, ZipCode, EpTelephone, EpFax, EcAddress1, EcAddress2, EcAddress3, EcAddress4, EcMobile, EEmail, EDOB, NIC, EPassportNO, EPassportExpiryDate,
 EVisaExpiryDate, MaritialStatus, Nationality, CreatedDate, CreatedPC, `Timestamp`, Erp_companyID FROM srp_employeesdetails
                                           WHERE Erp_companyID = " . current_companyID() . " AND EIdNo =" . $this->input->post('selectedItemsSync') . "  ")->row_array();


        if($result)
        {
            $this->db->set('EIdNo', $result["EIdNo"]);
            $this->db->set('serialNo', $result["serialNo"]);
            $this->db->set('EmpSecondaryCode', $result["EmpSecondaryCode"]);
            $this->db->set('EmpTitleId', $result["EmpTitleId"]);
            $this->db->set('EmpDesignationId', $result["EmpDesignationId"]);
            $this->db->set('Ename2', $result["Ename2"]);
            $this->db->set('Ename3', $result["Ename3"]);
            $this->db->set('Ename4', $result["Ename4"]);
            $this->db->set('initial', $result["initial"]);
            $this->db->set('EmpShortCode', $result["EmpShortCode"]);
            $this->db->set('EmpImage', $result["EmpImage"]);
            $this->db->set('EpAddress1', $result["EpAddress1"]);
            $this->db->set('EpAddress2', $result["EpAddress2"]);
            $this->db->set('EpAddress3', $result["EpAddress3"]);
            $this->db->set('EpAddress4', $result["EpAddress4"]);
            $this->db->set('ZipCode', $result["ZipCode"]);
            $this->db->set('EpFax', $result["EpFax"]);
            $this->db->set('EcAddress1', $result["EcAddress1"]);
            $this->db->set('EcAddress2', $result["EcAddress2"]);
            $this->db->set('EcAddress3', $result["EcAddress3"]);
            $this->db->set('EcAddress4', $result["EcAddress4"]);
            $this->db->set('EcMobile', $result["EcMobile"]);
            $this->db->set('EDOB', $result["EDOB"]);
            $this->db->set('NIC', $result["NIC"]);
            $this->db->set('EPassportNO', $result["EPassportNO"]);
            $this->db->set('EPassportExpiryDate', $result["EPassportExpiryDate"]);
            $this->db->set('EVisaExpiryDate', $result["EVisaExpiryDate"]);
            $this->db->set('MaritialStatus', $result["MaritialStatus"]);
            $this->db->set('Nationality', $result["Nationality"]);
            $this->db->set('CreatedDate', $result["CreatedDate"]);
            $this->db->set('CreatedPC', $result["CreatedPC"]);
            $this->db->set('Erp_companyID', $result["Erp_companyID"]);
            $this->db->set('isFromErp', 1);
            $this->db->where('crewID', $this->input->post('crewID'));
            $update = $this->db->update('srp_erp_mfq_crews');
            if ($update) {
                $this->session->set_flashdata('s', 'Records added Successfully');
                return array('status' => true);
            }
            else{
                $this->session->set_flashdata('e', 'Records adding failed');
                return array('status' => false);
            }
        }

    }
}
