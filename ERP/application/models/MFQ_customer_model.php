<?php

class MFQ_customer_model extends ERP_Model
{

    function add_customer()
    {
        $result = $this->db->query('INSERT INTO srp_erp_mfq_customermaster ( CustomerAutoID, CustomerSystemCode, CustomerName, partyCategoryID, CustomerAddress1, customerAddress2, customerCountry, customerTelephone, customerEmail, customerUrl, customerFax, secondaryCode, customerCurrencyID, customerCurrency, customerCurrencyDecimalPlaces, isActive, companyID, companyCode, createdUserGroup, createdPCID, createdUserID, createdUserName, createdDateTime, modifiedPCID, modifiedUserID, modifiedUserName, modifiedDateTime, `timestamp` ) 
                                SELECT customerAutoID,  customerSystemCode, customerName, partyCategoryID, customerAddress1, customerAddress2, customerCountry, customerTelephone, customerEmail, customerUrl, customerFax, secondaryCode, customerCurrencyID, customerCurrency, customerCurrencyDecimalPlaces, isActive, companyID, companyCode, createdUserGroup, createdPCID, createdUserID, createdUserName, createdDateTime, modifiedPCID, modifiedUserID, modifiedUserName, modifiedDateTime, `timestamp` 
 FROM srp_erp_customermaster 
                                WHERE companyID = ' . current_companyID() . '  AND customerAutoID IN(' . join(",", $this->input->post('selectedItemsSync')) . ')');

        if ($result) {
            $this->session->set_flashdata('s', 'Records added Successfully');
            return array('status' => true);
        }
    }

    function insert_customer()
    {
        $companyID = current_companyID();
        $isDefault = $this->input->post('default');
        $customerEmail = $this->input->post('customerEmail');

        foreach($customerEmail as $id)
        {
            $ismail =  $this->db->query("SELECT email From srp_erp_mfq_customeremail WHERE companyID=$companyID AND email = '$id'")->row_array();
        }
        if(!empty($ismail)) {
            return array('error' => 1, 'message' => 'Email Already Exists');
        }
        $post['CustomerName'] = trim($this->input->post('CustomerName') ?? '');
        $post['customerCountry'] = trim($this->input->post('customerCountry') ?? '');
        $post['CustomerAddress1'] = trim($this->input->post('CustomerAddress1') ?? '');
        $post['customerTelephone'] = trim($this->input->post('customerTelephone') ?? '');
        $datetime = format_date_mysql_datetime();
        $post['companyID'] = current_companyID();
        $post['createdUserID'] = current_userID();
        $post['createdPCID'] = current_pc();
        $post['createdDateTime'] = $datetime;
        $post['timestamp'] = $datetime;
        $post['isFromERP'] = 0;
        $post['preQualifiedYN'] = trim($this->input->post('preQualifiedYN') ?? '');

        $result = $this->db->insert('srp_erp_mfq_customermaster', $post);
        $last_id = $this->db->insert_id();

        if ($last_id) {

            foreach ($_POST['customerEmail'] as $key => $row) {
                $data['mfqCustomerAutoID'] = $last_id;
                $data['email'] = $row;
                $post['companyID'] = current_companyID();
                $data['isDefault'] = $isDefault[$key];
                $this->db->insert('srp_erp_mfq_customeremail', $data);
            }
        }

        if ($result) {
            return array('error' => 0, 'message' => 'Customer successfully Added', 'code' => 1);
        } else {
            return array('error' => 1, 'message' => 'Code: ' . $this->db->_error_number() . ' <br/>Message: ' . $this->db->_error_message());
        }

    }

    function get_srp_erp_mfq_customers()
    {
        $mfqCustomerAutoID = $this->input->post('mfqCustomerAutoID');
        $this->db->select('*');
        $this->db->from('srp_erp_mfq_customermaster');
        $this->db->where('mfqCustomerAutoID', $mfqCustomerAutoID);
        $data['master'] = $this->db->get()->row_array();

        $this->db->select('customerEmailAutoID,email,isDefault');
        $this->db->from('srp_erp_mfq_customeremail');
        $this->db->where('mfqCustomerAutoID', $mfqCustomerAutoID);
        $data['details'] = $this->db->get()->result_array();
        return $data;
    }

    function update_customer()
    {
        $companyID = current_companyID();
        $isDefault = $this->input->post('default');
        $customerEmail = $this->input->post('customerEmail');
        $mfqCustomerAutoID = $this->input->post('mfqCustomerAutoID');
        $customerEmailAutoID = $this->input->post('customerEmailid');
        $mail_list = [];
        $updatemail_list = [];
        foreach ($customerEmail as $key => $id) {
            $mailID = $customerEmailAutoID[$key];

            if (empty($customerEmailAutoID[$key])) {
                $mail_list[] = $id;
            }
            if ($customerEmailAutoID[$key]) {
                $updatemail_list[] = $id;
            }
        }
        if(!empty($updatemail_list))
        {
            $updatemail_list = "'".implode("','", $updatemail_list)."'";
            $ismailupdate = $this->db->query("SELECT email From srp_erp_mfq_customeremail WHERE companyID=$companyID AND mfqCustomerAutoID <> $mfqCustomerAutoID AND email IN ($updatemail_list) ")->result_array();
            if($ismailupdate)
            {
                return array('error' => 1, 'message' => 'Email Already Exists');
                exit();
            }
        }

      if(!empty($mail_list))
      {
          $mail_list = "'".implode("','", $mail_list)."'";

          $ismail = $this->db->query("SELECT email From srp_erp_mfq_customeremail WHERE companyID=$companyID AND email IN ($mail_list) ")->result_array();
          if($ismail)
          {
              return array('error' => 1, 'message' => 'Email Already Exists');
          }
      }

        $post['CustomerName'] = trim($this->input->post('CustomerName') ?? '');
        $post['customerCountry'] = trim($this->input->post('customerCountry') ?? '');
        $post['CustomerAddress1'] = trim($this->input->post('CustomerAddress1') ?? '');
        $post['customerTelephone'] = trim($this->input->post('customerTelephone') ?? '');
        $post['preQualifiedYN'] = trim($this->input->post('preQualifiedYN') ?? '');

        $post['modifiedUserID'] = current_userID();
        $post['modifiedPCID'] = current_pc();
        $post['modifiedDateTime'] = format_date_mysql_datetime();


        $this->db->where('mfqCustomerAutoID', $mfqCustomerAutoID);
        $result = $this->db->update('srp_erp_mfq_customermaster', $post);
        foreach ($customerEmail as $key => $row) {
            $mailID = $customerEmailAutoID[$key];
            //$ismail = null;
                $data['mfqCustomerAutoID'] = $mfqCustomerAutoID;
                $data['email'] = "$row";
                $data['companyID'] = current_companyID();
                $data['isDefault'] = $isDefault[$key];

                if (empty($customerEmailAutoID[$key])) {

                    $this->db->insert('srp_erp_mfq_customeremail', $data);
                } else {

                    $this->db->where('customerEmailAutoID', $customerEmailAutoID[$key]);
                    $this->db->update('srp_erp_mfq_customeremail', $data);

                }
        }
        if ($result) {
            return array('error' => 0, 'message' => 'Customer updated successfully', 'code' => 2);
        } else {
            return array('error' => 1, 'message' => 'Code: ' . $this->db->_error_number() . ' <br/>Message: ' . $this->db->_error_message());
        }

    }
    function delete_mail()
    {
        $this->db->delete('srp_erp_mfq_customeremail', array('customerEmailAutoID' => trim($this->input->post('customerEmailAutoID') ?? '')));
        return true;
    }

    function link_customer()
    {
        $result = $this->db->query('SELECT customerAutoID,customerSystemCode, partyCategoryID, customerAddress1, customerAddress2, customerCountry, customerTelephone, customerEmail, customerUrl, customerFax, secondaryCode, customerCurrencyID, customerCurrency, customerCurrencyDecimalPlaces, isActive
 FROM srp_erp_customermaster 
                                WHERE companyID = ' . current_companyID() . '  AND customerAutoID =' .$this->input->post('selectedItemsSync'))->row_array();

        if ($result) {
            if ($result) {
                $this->db->set('CustomerAutoID', $result["customerAutoID"]);
                $this->db->set('CustomerSystemCode', $result["customerSystemCode"]);
                $this->db->set('partyCategoryID', $result["partyCategoryID"]);
                $this->db->set('customerAddress2', $result["customerAddress2"]);
                $this->db->set('customerUrl', $result["customerUrl"]);
                $this->db->set('customerFax', $result["customerFax"]);
                $this->db->set('secondaryCode', $result["secondaryCode"]);
                $this->db->set('customerCurrencyID', $result["customerCurrencyID"]);
                $this->db->set('customerCurrency', $result["customerCurrency"]);
                $this->db->set('customerCurrencyDecimalPlaces', $result["customerCurrencyDecimalPlaces"]);
                $this->db->set('isActive', $result["isActive"]);
                $this->db->set('isFromErp', 1);
                $this->db->where('mfqCustomerAutoID', $this->input->post('mfqCustomerAutoID'));
                $update = $this->db->update('srp_erp_mfq_customermaster');

                if ($update) {
                    $this->session->set_flashdata('s', 'Records updated Successfully');
                    return array('status' => true);
                }
                else{
                    $this->session->set_flashdata('e', 'Records adding failed');
                    return array('status' => false);
                }
            }

        }
    }


}
