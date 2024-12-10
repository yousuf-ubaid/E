<?php

class MFQ_CustomerMaster extends ERP_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('MFQ_customer_model');
    }

    function fetch_customer()
    {
        $this->datatables->select('rowTbl.customerName as masterCustomername, masterTbl.CustomerName  AS  CustomerName, masterTbl.mfqCustomerAutoID as mfqCustomerAutoID, masterTbl.CustomerAutoID as CustomerAutoID, masterTbl.serialNo as  serialNo, masterTbl.customerCountry AS  customerCountry, masterTbl.customerTelephone AS customerTelephone, masterTbl.customerEmail AS customerEmail, masterTbl.isFromERP as isFromERP, masterTbl.CustomerAddress1 AS CustomerAddress1', false)
            ->from('srp_erp_mfq_customermaster as masterTbl')
            ->join('srp_erp_customermaster rowTbl','rowTbl.CustomerAutoID = masterTbl.CustomerAutoID', 'left')
            ->where('masterTbl.companyID', current_companyID());
        $this->datatables->add_column('countryDiv', '$1', 'countryDiv(customerCountry)');
        $this->datatables->add_column('edit', '$1', 'edit_mfq_customer(mfqCustomerAutoID, isFromERP)');

        $result = $this->datatables->generate();
        echo $result;
    }

    function fetch_sync_customer()
    {
        $this->datatables->select('masterTbl.customerAutoID as customerAutoID, masterTbl.customerSystemCode as customerSystemCode, masterTbl.customerName as customerName, masterTbl.customerTelephone as customerTelephone, masterTbl.customerAddress1 as customerAddress1, masterTbl.customerEmail as customerEmail, masterTbl.customerCountry as customerCountry', false)
            ->from('srp_erp_customermaster as masterTbl')
            ->where('companyID', current_companyID());
        $this->datatables->where('NOT EXISTS(SELECT * FROM srp_erp_mfq_customermaster WHERE srp_erp_mfq_customermaster.CustomerAutoID = masterTbl.CustomerAutoID AND companyID =' . current_companyID() . ' )');

        $this->datatables->add_column('countryDiv', '$1', 'countryDiv(customerCountry)');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="selectItem_$1" onclick="ItemsSelectedSync(this)" type="checkbox" class="columnSelected"  value="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'customerAutoID');

        $result = $this->datatables->generate();
        echo $result;
    }

    function fetch_link_customer()
    {
        $this->datatables->select('masterTbl.customerAutoID as customerAutoID, masterTbl.customerSystemCode as customerSystemCode, masterTbl.customerName as customerName, masterTbl.customerTelephone as customerTelephone, masterTbl.customerAddress1 as customerAddress1, masterTbl.customerEmail as customerEmail, masterTbl.customerCountry as customerCountry', false)
            ->from('srp_erp_customermaster as masterTbl')
            ->where('companyID', current_companyID());
//        $this->datatables->where('NOT EXISTS(SELECT * FROM srp_erp_mfq_customermaster WHERE srp_erp_mfq_customermaster.CustomerAutoID = masterTbl.CustomerAutoID AND companyID =' . current_companyID() . ' )');
        $this->datatables->add_column('countryDiv', '$1', 'countryDiv(customerCountry)');
        $this->datatables->add_column('edit', '<div style="text-align: center;"><div class="skin skin-square item-iCheck"> <div class="skin-section extraColumns"><input id="linkItem_$1" name="linkItem" type="radio"  value="$1" class="radioChk" data-itemAutoID="$1" ><label for="checkbox">&nbsp;</label> </div></div></div>', 'customerAutoID');

        $result = $this->datatables->generate();
        echo $result;
    }

    function add_customers()
    {
        $this->form_validation->set_rules('selectedItemsSync[]', 'Item', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->MFQ_customer_model->add_customer());
        }
    }

    function add_edit_customer()
    {
        $mfqCustomerAutoID = $this->input->post('mfqCustomerAutoID');

        $this->form_validation->set_rules('CustomerName', 'Customer Name', 'trim|required');
        $this->form_validation->set_rules('customerCountry', 'Country', 'trim|required');
       /* $this->form_validation->set_rules('customerEmail', 'Email', 'trim|required');*/
        $this->form_validation->set_rules('CustomerAddress1', 'Address', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            echo json_encode(array('error' => 1, 'message' => validation_errors()));
        } else {
            if ($mfqCustomerAutoID) {
                /** Update */
                echo json_encode($this->MFQ_customer_model->update_customer());
            } else {
                /** Insert */
                echo json_encode($this->MFQ_customer_model->insert_customer());
            }
        }
    }

    function loadCustomerDetail()
    {
        $result = $this->MFQ_customer_model->get_srp_erp_mfq_customers();
        if (!empty($result)) {
            echo json_encode(array_merge(array('error' => 0, 'message' => 'Loading customer detail'), $result));
        } else {
            echo json_encode(array('error' => 1, 'message' => 'record not found!'));
        }
    }



    function delete_mail()
    {
        echo json_encode($this->MFQ_customer_model->delete_mail());
    }


    function link_customer()
    {
        $this->form_validation->set_rules('selectedItemsSync', 'Item', 'required');
        if ($this->form_validation->run() == FALSE) {
            $this->session->set_flashdata('e', validation_errors());
            echo json_encode(FALSE);
        } else {
            echo json_encode($this->MFQ_customer_model->link_customer());
        }
    }

}
